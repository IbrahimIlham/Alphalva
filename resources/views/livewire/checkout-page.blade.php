<div>
    <div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto ">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">
            Checkout
        </h1>
        <form action="" wire:submit.prevent="checkout">
            <div class="grid grid-cols-12 gap-4">
                <div class="md:col-span-12 lg:col-span-8 col-span-12">
                    <!-- Card: Shipping & Address -->
                    <div class="bg-white rounded-xl shadow p-4 sm:p-7 dark:bg-slate-900">
                        <!-- Origin Info -->
                        <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900 rounded-lg">
                            <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-1">📦 Pengiriman dari:</h3>
                            <p class="text-sm text-blue-700 dark:text-blue-300">Jagakarsa, Jakarta Selatan</p>
                            <p class="text-xs text-blue-600 dark:text-blue-400">Jl. Kelapa Hijau No. 59</p>
                        </div>
                        
                        <!-- Shipping Info -->
                        <div class="mb-6">
                            <h2 class="text-xl font-bold underline text-gray-700 dark:text-white mb-4">Shipping Address</h2>
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-gray-700 dark:text-white mb-1" for="province">Province</label>
                                    <select wire:model="selected_province" wire:change="getCities" id="province" class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none @error('selected_province') border-red-500 @enderror">
                                        <option value="">Pilih Provinsi</option>
                                        @foreach($provinces as $province)
                                            <option value="{{ $province['province_id'] }}">{{ $province['province'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('selected_province')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                                </div>
                                <div>
                                    <label class="block text-gray-700 dark:text-white mb-1" for="city">City</label>
                                    <select wire:model="selected_city" wire:change="$set('shipping_method', '')" id="city" class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none @error('selected_city') border-red-500 @enderror">
                                        <option value="">Pilih Kota</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city['city_id'] }}">{{ $city['city_name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('selected_city')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 dark:text-white mb-1" for="shipping_method">Shipping Method</label>
                                <select wire:model="shipping_method" wire:change="getCost" id="shipping_method" class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none @error('shipping_method') border-red-500 @enderror">
                                    <option value="">Pilih Metode Pengiriman</option>
                                    <option value="jne">JNE</option>
                                    <option value="pos">POS Indonesia</option>
                                    <option value="tiki">TIKI</option>
                                </select>
                                @error('shipping_method')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                                
                                @if(session()->has('shipping_error'))
                                    <div class="mt-2 p-2 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                                        {{ session('shipping_error') }}
                                    </div>
                                @endif
                            </div>
                            <div class="mt-4">
                                <label class="block text-gray-700 dark:text-white mb-1" for="address">Address (Tulis alamat lengkap beserta dengan Kelurahan dan Kecamatan)</label>
                                <input wire:model="street_address" class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none @error('street_address') border-red-500 @enderror" id="address" type="text">
                                @error('street_address')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-gray-700 dark:text-white mb-1" for="zip">ZIP Code</label>
                                    <input wire:model="zip_code" class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none @error('zip_code') border-red-500 @enderror" id="zip" type="text">
                                    @error('zip_code')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <!-- Shipping Address -->
                        <div class="mb-6">
                            <h2 class="text-xl font-bold underline text-gray-700 dark:text-white mb-2">Identity</h2>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 dark:text-white mb-1" for="first_name">First Name</label>
                                    <input wire:model="first_name" class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none @error('first_name') border-red-500 @enderror" id="first_name" type="text">
                                    @error('first_name')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                                </div>
                                <div>
                                    <label class="block text-gray-700 dark:text-white mb-1" for="last_name">Last Name</label>
                                    <input wire:model="last_name" class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none @error('last_name') border-red-500 @enderror" id="last_name" type="text">
                                    @error('last_name')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-gray-700 dark:text-white mb-1" for="phone">Phone</label>
                                <input wire:model="phone" class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none @error('phone') border-red-500 @enderror" id="phone" type="text">
                                @error('phone')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                            </div>
                            
                        </div>
                        <div class="text-lg font-semibold mb-4 dark:text-white">
                            Select Payment Method
                        </div>
                        <ul class="grid w-full gap-6 md:grid-cols-2">
                            <li>
                                <input wire:model="payment_method" class="hidden peer" id="hosting-big" type="radio"
                                    value="midtrans">
                                <label
                                    class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-blue-500 peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700"
                                    for="hosting-big">
                                    <div class="block">
                                        <div class="w-full text-lg font-semibold">
                                            midtrans
                                        </div>
                                    </div>
                                    <svg aria-hidden="true" class="w-5 h-5 ms-3 rtl:rotate-180" fill="none"
                                        viewbox="0 0 14 10" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 5h12m0 0L9 1m4 4L9 9" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2">
                                        </path>
                                    </svg>
                                </label>
                                </input>
                            </li>
                        </ul>
                        @error('payment_method')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- End Card -->
                </div>
                <div class="md:col-span-12 lg:col-span-4 col-span-12">
                    <div class="bg-white rounded-xl shadow p-4 sm:p-7 dark:bg-slate-900">
                        <div class="text-xl font-bold underline text-gray-700 dark:text-white mb-2">
                            ORDER SUMMARY
                        </div>
                        <div class="flex justify-between mb-2 font-bold">
                            <span class="dark:text-white">
                                Subtotal
                            </span>
                            <span class="dark:text-white">
                                {{ Number::currency($grand_total, 'IDR') }}
                            </span>
                        </div>
                        <div class="flex justify-between mb-2 font-bold">
                            <span class="dark:text-white">
                                Shipping Cost
                                @if($shipping_method)
                                    ({{ strtoupper($shipping_method) }})
                                @endif
                            </span>
                            <span class="dark:text-white">
                                {{ Number::currency($shipping_cost ?? 0, 'IDR') }}
                            </span>
                        </div>
                        <hr class="bg-slate-400 my-4 h-1 rounded">
                        <div class="flex justify-between mb-2 font-bold">
                            <span class="dark:text-white">
                                Grand Total
                            </span>
                            <span class="dark:text-white">
                                {{ Number::currency($grand_total + ($shipping_cost ?? 0), 'IDR') }}
                            </span>
                        </div>
                        </hr>
                    </div>
                    <button type="submit"
                        class="bg-blue-700 mt-4 w-full p-3 rounded-lg text-lg text-white hover:bg-blue-400">
                        
                        <span wire:loading.remove>Place Order</span>
                        <span wire:loading>Processing...</span>
                    </button>
                    <div class="bg-white mt-4 rounded-xl shadow p-4 sm:p-7 dark:bg-slate-900">
                        <div class="text-xl font-bold underline text-gray-700 dark:text-white mb-2">
                            BASKET SUMMARY
                        </div>
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700" role="list">
                            @foreach ($cart_items as $item)
                                <li class="py-3 sm:py-4" wire:key="{{ $item['product_id'] }}">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <img alt="Neil image" class="w-12 h-12 rounded-full"
                                                src="{{ url('storage', $item['image']) }}">
                                            </img>
                                        </div>
                                        <div class="flex-1 min-w-0 ms-4">
                                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                                {{ $item['name'] }}
                                            </p>
                                            <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                                Quantity: {{ $item['quantity'] }}
                                            </p>
                                        </div>
                                        <div
                                            class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                            {{ Number::currency($item['total_amount'], 'IDR') }}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                            < </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
