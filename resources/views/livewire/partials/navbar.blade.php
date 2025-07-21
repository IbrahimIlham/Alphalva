<div wire:key="{{ auth()->id() }}">
  <div>
    <header class="flex fixed z-50 top-0 flex-wrap md:justify-start md:flex-nowrap w-full bg-gray-900 text-sm py-3 md:py-0 shadow-md">
      <nav class="max-w-[85rem] w-full mx-auto px-4 md:px-6 lg:px-8" aria-label="Global">
        <div class="md:flex md:items-center md:justify-between">
          <div class="flex items-center justify-between">
            <a class="flex items-center gap-2 flex-none text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-blue-200" href="/" aria-label="Brand">
              <img src="{{ asset('images/logos.png') }}" alt="Logo" class="h-8 w-8 rounded-full object-cover border-2 border-white shadow-md">
              alphalva
            </a>

            <div class="md:hidden">
              <button type="button" class="hs-collapse-toggle flex justify-center items-center w-9 h-9 text-sm font-semibold rounded-lg border border-gray-700 text-white hover:bg-gray-800 focus:outline-none disabled:opacity-50 disabled:pointer-events-none" data-hs-collapse="#navbar-collapse-with-animation" aria-controls="navbar-collapse-with-animation" aria-label="Toggle navigation">
                <!-- menu -->
                <svg class="hs-collapse-open:hidden flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor">
                  <line x1="3" x2="21" y1="6" y2="6" />
                  <line x1="3" x2="21" y1="12" y2="12" />
                  <line x1="3" x2="21" y1="18" y2="18" />
                </svg>
                <!-- close -->
                <svg class="hs-collapse-open:block hidden flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor">
                  <path d="M18 6 6 18" />
                  <path d="m6 6 12 12" />
                </svg>
              </button>
            </div>
          </div>

          <div id="navbar-collapse-with-animation" class="hs-collapse hidden overflow-hidden transition-all duration-300 basis-full grow md:block">
            <div class="overflow-hidden overflow-y-auto max-h-[75vh] [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-slate-700 dark:[&::-webkit-scrollbar-thumb]:bg-slate-500">
              <div class="flex flex-col gap-x-0 mt-5 divide-y divide-dashed divide-gray-200 md:flex-row md:items-center md:justify-end md:gap-x-7 md:mt-0 md:ps-7 md:divide-y-0 md:divide-solid dark:divide-gray-700">

                <a class="font-medium flex items-center py-3 md:py-6 transition-all duration-200 {{ request()->is('/') ? 'text-blue-400 border-b-2 border-blue-400' : 'text-white hover:text-blue-400' }}" href="/" wire:navigate>Home</a>

                <a class="font-medium flex items-center py-3 md:py-6 transition-all duration-200 {{ request()->is('categories') ? 'text-blue-400 border-b-2 border-blue-400' : 'text-white hover:text-blue-400' }}" href="/categories" wire:navigate>Categories</a>

                <a class="font-medium flex items-center py-3 md:py-6 transition-all duration-200 {{ request()->is('products') ? 'text-blue-400 border-b-2 border-blue-400' : 'text-white hover:text-blue-400' }}" href="/products" wire:navigate>Products</a>

                <a class="font-medium flex items-center py-3 md:py-6 transition-all duration-200 {{ request()->is('cart') ? 'text-blue-400 border-b-2 border-blue-400' : 'text-white hover:text-blue-400' }}" href="/cart" wire:navigate>
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                  </svg>
                  <span class="mr-1">Cart</span>
                  <span class="py-0.5 px-1.5 rounded-full text-xs font-medium bg-white border border-blue-700 text-blue-700">{{ $total_count }}</span>
                </a>



                @guest
                <div class="pt-3 md:pt-0">
                  <a class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-md border border-[#181c24] bg-gradient-to-l from-gray-900 via-blue-800 to-blue-400 text-white hover:from-blue-400 hover:to-[#12151c] transition-colors duration-300 ease-in-out disabled:opacity-50 disabled:pointer-events-none" href="/login" wire:navigate>
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor">
                      <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                      <circle cx="12" cy="7" r="4" />
                    </svg>
                    Log in
                  </a>
                </div>
                @endguest

                @auth
                <div class="hs-dropdown relative md:py-4">
                  <button type="button" class="flex items-center text-white hover:text-blue-400 font-medium">
                    {{ auth()->user()->name }}
                    <svg class="ms-2 w-4 h-4 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor">
                      <path d="m6 9 6 6 6-6" />
                    </svg>
                  </button>
                  <div class="hs-dropdown-menu min-w-[12rem] absolute z-50 mt-2 bg-white rounded-lg shadow-md p-2 end-0 right-0 left-auto top-full hidden border border-gray-700">
                    <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-900 hover:bg-gray-100 hover:text-blue-700" wire:navigate href="/my-orders">My Orders</a>
                    <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-900 hover:bg-gray-100 hover:text-blue-700" href="/logout">Logout</a>
                  </div>
                </div>
                @endauth

              </div>
            </div>
          </div>
        </div>
      </nav>
    </header>
  </div>
</div>
