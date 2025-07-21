<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Helpers\RajaOngkirHelper;
use App\Mail\OrderPlaced;
use App\Models\Addresses;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Title;
use Livewire\Component;
use Stripe\Checkout\Session;
use Stripe\Stripe;

#[Title('Checkout')]
class CheckoutPage extends Component
{
    public $first_name;
    public $last_name;
    public $phone;
    public $street_address;
    public $city;
    public $province;
    public $zip_code;
    public $payment_method;
    public $shipping_method; // Property yang hilang
    // RajaOngkir integration
    public $provinces = [];
    public $cities = [];
    public $selected_province;
    public $selected_city;
    public $shipping_cost = 0;
    
    public function mount() {
        $cart_items = CartManagement::getCartItemsFromCookie();
        if(count($cart_items) == 0  ) {
            return redirect('/products');
        }
        // Ambil data provinsi dari RajaOngkir
        $this->provinces = RajaOngkirHelper::getProvinces();
        
        // Debug: Log untuk memastikan data ter-load
        \Log::info('Provinces loaded:', ['count' => count($this->provinces)]);
    }

    public function checkout()
    {

        $this->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'street_address' => 'required',
            'selected_province' => 'required',
            'selected_city' => 'required',
            'zip_code' => 'required',
            'payment_method' => 'required',
            'shipping_method' => 'required',
        ]);

        $cart_items = CartManagement::getCartItemsFromCookie();
        $line_items = [];
        foreach ($cart_items as $item) {
            $line_items[] = [
                'price_data' => [
                    'currency' => 'idr',
                    'unit_amount' => $item['unit_amount'] * 100,
                    'product_data' => [
                        'name' => $item['name'],
                    ]
                ],
                'quantity' => $item['quantity'],
            ];
        }

        $order = new Order();
        $order->user_id = auth()->user()->id;
        $order->grand_total = CartManagement::calculateGrandTotal($cart_items);
        $order->payment_method = $this->payment_method;
        $order->payment_status = 'pending';
        $order->status = 'new';
        $order->currency = 'idr';
        // Set shipping amount dari RajaOngkir atau default
        $shipping_amount = $this->shipping_cost;
        if ($shipping_amount == 0) {
            // Fallback ke harga default jika tidak ada shipping cost dari RajaOngkir
            if ($this->shipping_method == 'jne') {
                $shipping_amount = 15000;
            } elseif ($this->shipping_method == 'pos') {
                $shipping_amount = 18000;
            } elseif ($this->shipping_method == 'tiki') {
                $shipping_amount = 20000;
            }
        }
        $order->shipping_amount = $shipping_amount;
        $order->shipping_method = $this->shipping_method;
        
        // Debug: Log order details
        \Log::info('Order created:', [
            'order_id' => $order->id,
            'shipping_method' => $this->shipping_method,
            'shipping_amount' => $shipping_amount,
            'origin' => 'Jagakarsa, Jakarta Selatan',
            'destination' => $this->selected_city
        ]);
        $order->notes = 'Order created by ' . auth()->user()->name;
        $order->save();

        $address = new Addresses();
        $address->first_name = $this->first_name;
        $address->last_name =  $this->last_name;
        $address->phone = $this->phone;
        $address->street_address = $this->street_address;
        
        // Ambil nama kota dan provinsi dari data RajaOngkir
        $selectedCity = collect($this->cities)->firstWhere('city_id', $this->selected_city);
        $selectedProvince = collect($this->provinces)->firstWhere('province_id', $this->selected_province);
        
        $address->city = $selectedCity['city_name'] ?? $this->selected_city;
        $address->province = $selectedProvince['province'] ?? $this->selected_province;
        $address->zip_code = $this->zip_code;
        $address->order_id = $order->id;
        $address->save();

        $order->items()->createMany($cart_items);

        // Midtrans config & Snap Token
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('midtrans.isProduction');
        \Midtrans\Config::$isSanitized = config('midtrans.isSanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is3ds');

        $params = [
            'transaction_details' => [
                'order_id' => 'order_' . $order->id . '_' . time(),
                'gross_amount' => $order->grand_total + $order->shipping_amount,
            ],
            'customer_details' => [
                'first_name' => $this->first_name,
                'email' => auth()->user()->email,
            ],
            // 'item_details' => ... (opsional, bisa diisi dari $cart_items)
        ];
        $snapToken = \Midtrans\Snap::getSnapToken($params);
        $order->snap_token = $snapToken;
        $order->save();

        CartManagement::clearCartItems();
        Mail::to(request()->user())->send(new OrderPlaced($order));

        // Redirect hanya ke Midtrans atau success (tanpa Stripe)
        if($this->payment_method == 'midtrans') {
            // Nanti di blade, trigger window.snap.pay($snapToken)
            return redirect()->route('checkout.midtrans', ['order' => $order->id]);
        } else {
            $redirect_url = route('success');
            return redirect($redirect_url);
        }
    }

    public function render()
    {
        $cart_items = CartManagement::getCartItemsFromCookie();
        $grand_total = CartManagement::calculateGrandTotal($cart_items);
        return view('livewire.checkout-page', [
            'cart_items' => $cart_items,
            'grand_total' => $grand_total,
            'provinces' => $this->provinces,
            'cities' => $this->cities,
            'selected_province' => $this->selected_province,
            'selected_city' => $this->selected_city,
            'shipping_cost' => $this->shipping_cost,
        ]);

    }

    // Ambil kota dari provinsi terpilih
    public function getCities()
    {
        $this->cities = [];
        $this->selected_city = null;
        $this->shipping_cost = 0;
        $this->shipping_method = '';
        
        if ($this->selected_province) {
            $this->cities = RajaOngkirHelper::getCities($this->selected_province);
        }
    }

    // Hitung ongkir dari kota & shipping method terpilih
    public function getCost()
    {
        $this->shipping_cost = 0;
        if ($this->selected_city && $this->shipping_method) {
            $weight = 1000; // berat total dalam gram, sesuaikan
            $origin = 153; // ID kota asal (Jakarta Selatan) - Jagakarsa
            
            // Cek apakah shipping method tersedia untuk kota ini
            $cost = RajaOngkirHelper::getCost($origin, $this->selected_city, $weight, $this->shipping_method);
            
            if ($cost > 0) {
                $this->shipping_cost = $cost;
                
                // Debug: Log shipping cost
                \Log::info('Shipping cost calculated:', [
                    'origin' => $origin,
                    'destination' => $this->selected_city,
                    'courier' => $this->shipping_method,
                    'cost' => $this->shipping_cost
                ]);
            } else {
                // Reset shipping method jika tidak tersedia
                $currentMethod = $this->shipping_method;
                $this->shipping_method = '';
                session()->flash('shipping_error', 'Layanan ' . strtoupper($currentMethod) . ' tidak tersedia untuk kota ini.');
            }
        }
    }
}
