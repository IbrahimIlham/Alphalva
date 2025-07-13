<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
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
    public $shipping_method = 'jne';
    
    public function mount() {
        $cart_items = CartManagement::getCartItemsFromCookie();

        if(count($cart_items) == 0  ) {
            return redirect('/products');
        }
    }

    public function checkout()
    {

        $this->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'street_address' => 'required',
            'city' => 'required',
            'province' => 'required',
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
        // Set shipping amount berdasarkan metode
        $shipping_amount = 0;
        if ($this->shipping_method == 'jne') {
            $shipping_amount = 15000;
        } elseif ($this->shipping_method == 'jnt') {
            $shipping_amount = 18000;
        } elseif ($this->shipping_method == 'sicepat') {
            $shipping_amount = 20000;
        } elseif ($this->shipping_method == 'pickup') {
            $shipping_amount = 0;
        }
        $order->shipping_amount = $shipping_amount;
        $order->shipping_method = $this->shipping_method;
        $order->notes = 'Order created by ' . auth()->user()->name;
        $order->save();

        $address = new Addresses();
        $address->first_name = $this->first_name;
        $address->last_name =  $this->last_name;
        $address->phone = $this->phone;
        $address->street_address = $this->street_address;
        $address->city = $this->city;
        $address->province = $this->province;
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
                'order_id' => $order->id,
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
        $shipping = 0;
        if ($this->shipping_method == 'jne') {
            $shipping = 15000;
        } elseif ($this->shipping_method == 'jnt') {
            $shipping = 18000;
        } elseif ($this->shipping_method == 'sicepat') {
            $shipping = 20000;
        }
        return view('livewire.checkout-page', [
            'cart_items' => $cart_items,
            'grand_total' => $grand_total,
            'shipping_method' => $this->shipping_method,
            'shipping' => $shipping,
        ]);
    }
}
