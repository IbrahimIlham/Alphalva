<div>
    <div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto bg-gradient-to-b from-gray-100 via-gray-300 to-gray-400">
        <div class="container mx-auto px-4 mt-10 ">
          <h1 class="text-2xl font-semibold mb-4">Shopping Cart</h1>
          <div class="flex flex-col md:flex-row gap-4">
            <div class="md:w-3/4">
              <div class="bg-white overflow-x-auto rounded-lg shadow-md p-6 mb-4">
                <table class="w-full">
                  <thead>
                    <tr>
                      <th class="text-left font-semibold">Product</th>
                      <th class="text-left font-semibold">Price</th>
                      <th class="text-left font-semibold">Quantity</th>
                      <th class="text-left font-semibold">Total</th>
                      <th class="text-left font-semibold">Remove</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($cart_items as $item)
                    <tr wire:key="{{ $item['product_id'] }}">
                      <td class="py-4">
                        <div class="flex flex-col md:flex-row items-start md:items-center min-w-0">
                          <img class="h-12 w-12 md:h-16 md:w-16 mb-2 md:mb-0 mr-0 md:mr-4 flex-shrink-0" src="{{ url('storage', $item['image']) }}" alt="Product image">
                          <span class="font-semibold text-xs md:text-base break-words">{{ $item['name'] }}</span>
                        </div>
                      </td>
                      <td class="py-4">{{ Number::currency($item['unit_amount'],'IDR') }}</td>
                      <td class="py-4">

                        <div class="flex items-center">
                          <button  wire:click='decreaseQty({{ $item['product_id'] }})' class="border rounded-md py-2 px-4 mr-2">-</button>
                          <span class="text-center w-8">{{ $item['quantity'] }}</span>
                          <button wire:click='increaseQty({{ $item['product_id'] }})' class="border rounded-md py-2 px-4 ml-2">+</button>
                        </div>
                      </td>
                      <td class="py-4">{{ Number::currency($item['total_amount'],'IDR') }}</td>
                      <td><button wire:click='removeItem({{ $item['product_id'] }})' class="bg-slate-300 border-2 border-slate-400 rounded-lg px-3 py-1 hover:bg-red-500 hover:text-white hover:border-red-700">
                        <span wire:loading.remove wire:target="removeItem({{ $item['product_id'] }})">Remove</span><span wire:loading wire:target="removeItem({{ $item['product_id'] }})">Remove...</span>  
                      </button></td>
                    </tr>
                    @empty
                        <tr>
                          <td colspan="5" class="text-center text-gray-500 font-semibold py-4">None</td>
                        </tr>
                    @endforelse
                   
                    <!-- More product rows -->
                  </tbody>
                </table>
              </div>
            </div>
            <div class="md:w-1/4">
              <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">Summary</h2>
                <div class="flex justify-between mb-2">
                  <span>Subtotal</span>
                  <span>{{ Number::currency($grand_total, 'IDR') }}</span>
                </div>
                <div class="flex justify-between mb-2">
                  <span>Shipping</span>
                  <span>{{ Number::currency(0, 'IDR') }}</span>
                </div>
                <hr class="my-2">
                <div class="flex justify-between mb-2">
                  <span class="font-semibold">Total</span>
                  <span class="font-semibold">{{ Number::currency($grand_total, 'IDR') }}</span>
                </div>
                @if ($cart_items)
                  <a href="/checkout" class="block text-center bg-blue-500 text-white py-2 px-4 rounded-lg mt-4 w-full">Checkout</a href="/checkout">
                @endif 
              </div>
            </div>
          </div>
        </div>
      </div>
</div>
