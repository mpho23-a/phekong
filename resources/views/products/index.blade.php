<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Products</h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 sm:pb-6">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <form method="GET" class="mb-4">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                    class="w-full border rounded-lg pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                @if (request('search'))
                    <a href="{{ route('products.index') }}"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </div>
        </form>

        {{-- Page-specific action: always visible, not duplicated in bottom nav --}}
        @if (auth()->user()->hasRole('stock_admin'))
            <a href="{{ route('products.create') }}"
                class="inline-block mb-4 px-4 py-2 bg-indigo-600 text-white rounded text-sm">
                + Add Product
            </a>
        @endif

        {{-- Everything below duplicates the bottom nav — desktop only, since phones already have these as tabs --}}
        <div class="hidden sm:flex flex-wrap gap-2 mb-4">
            @if (auth()->user()->hasRole('stock_admin'))
                <a href="{{ route('sales.dashboard') }}"
                    class="px-4 py-2 bg-indigo-600 text-white rounded text-sm">Sales Dashboard</a>
                {{--   <a href="{{ route('sales.create') }}" class="px-4 py-2 bg-green-600 text-white rounded text-sm">Log Sale</a> --}}
                <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-green-600 text-white rounded text-sm">Sales
                    Report</a>
            @endif

            @if (auth()->user()->hasRole('approval_admin'))
                <a href="{{ route('stock-requests.index') }}"
                    class="px-4 py-2 bg-yellow-600 text-white rounded text-sm">Pending Stock Requests</a>
                <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-700 text-white rounded text-sm">Manage
                    Users</a>
            @endif

            @if (auth()->user()->hasRole('sales_rep'))
                <a href="{{ route('sales.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded text-sm">Log
                    Today's Sales</a>
            @endif
        </div>

        {{-- Mobile: card list --}}
        <div class="sm:hidden space-y-3">
            @forelse ($products as $product)
                <div
                    class="bg-white shadow rounded-xl overflow-hidden border-l-4 {{ $product->isLowStock() ? 'border-red-500' : 'border-green-500' }}">
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2">
                            <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                            @if ($product->isLowStock())
                                <span
                                    class="flex-shrink-0 px-2 py-0.5 text-[10px] font-medium bg-red-100 text-red-700 rounded-full">Low
                                    Stock</span>
                            @else
                                <span
                                    class="flex-shrink-0 px-2 py-0.5 text-[10px] font-medium bg-green-100 text-green-700 rounded-full">OK</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            R{{ number_format($product->price, 2) }} · {{ $product->quantity }} in stock
                        </p>

                        @if (auth()->user()->hasRole('stock_admin'))
                            <div class="flex items-center gap-4 mt-3 pt-3 border-t border-gray-100">
                                <a href="{{ route('products.edit', $product) }}" class="text-indigo-600"
                                    aria-label="Edit product">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 13.5v4.75A2.25 2.25 0 0117.25 20.5H5.75A2.25 2.25 0 013.5 18.25V6.75A2.25 2.25 0 015.75 4.5h4.75" />
                                    </svg>
                                </a>

                                <form action="{{ route('products.destroy', $product) }}" method="POST"
                                    onsubmit="return confirm('Delete this product?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600" aria-label="Delete product">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>

                                <button type="button"
                                    onclick="document.getElementById('stock-form-m-{{ $product->id }}').classList.toggle('hidden')"
                                    class="text-yellow-700 ml-auto" aria-label="Request stock update">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </button>
                            </div>

                            <div id="stock-form-m-{{ $product->id }}"
                                class="hidden mt-3 pt-3 border-t border-gray-100">
                                <form action="{{ route('stock-requests.store', $product) }}" method="POST"
                                    class="flex flex-col gap-2">
                                    @csrf
                                    <input type="number" name="requested_quantity" min="0"
                                        placeholder="New quantity" class="border rounded px-2 py-1.5 text-sm w-full"
                                        required>
                                    <input type="text" name="reason" placeholder="Reason (optional)"
                                        class="border rounded px-2 py-1.5 text-sm w-full">
                                    <button type="submit"
                                        class="px-3 py-1.5 bg-indigo-600 text-white rounded text-xs">Submit
                                        Request</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-6">
                    @if (request('search'))
                        No products match "{{ request('search') }}".
                    @else
                        No products yet.
                    @endif
                </p>
            @endforelse

        </div>

        {{-- Desktop: full table --}}
        <div class="hidden sm:block bg-white shadow rounded overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="p-3">Name</th>
                        <th class="p-3">Description</th>
                        <th class="p-3">Price</th>
                        <th class="p-3">Quantity</th>
                        <th class="p-3">Status</th>
                        @if (auth()->user()->hasRole('stock_admin'))
                            <th class="p-3">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr class="border-t {{ $product->isLowStock() ? 'bg-red-50' : '' }}">
                            <td class="p-3 font-medium">{{ $product->name }}</td>
                            <td class="p-3 text-gray-600">{{ Str::limit($product->description, 60) }}</td>
                            <td class="p-3">R{{ number_format($product->price, 2) }}</td>
                            <td class="p-3">{{ $product->quantity }}</td>
                            <td class="p-3">
                                @if ($product->isLowStock())
                                    <span class="px-2 py-1 text-xs bg-red-600 text-white rounded">Low Stock</span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-green-600 text-white rounded">OK</span>
                                @endif
                            </td>
                            @if (auth()->user()->hasRole('stock_admin'))
                                <td class="p-3 space-x-2">
                                    <a href="{{ route('products.edit', $product) }}"
                                        class="text-indigo-600 hover:underline">Edit</a>

                                    <form action="{{ route('products.destroy', $product) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Delete this product?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>

                                    <button type="button"
                                        onclick="document.getElementById('stock-form-{{ $product->id }}').classList.toggle('hidden')"
                                        class="text-yellow-700 hover:underline">
                                        Request Stock Update
                                    </button>

                                    <div id="stock-form-{{ $product->id }}" class="hidden mt-2">
                                        <form action="{{ route('stock-requests.store', $product) }}" method="POST"
                                            class="flex items-center gap-2">
                                            @csrf
                                            <input type="number" name="requested_quantity" min="0"
                                                placeholder="New qty" class="border rounded px-2 py-1 w-24" required>
                                            <input type="text" name="reason" placeholder="Reason"
                                                class="border rounded px-2 py-1">
                                            <button type="submit"
                                                class="px-3 py-1 bg-indigo-600 text-white rounded text-xs">Submit</button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-3 text-center text-gray-500">No products yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('partials.bottom-nav')
</x-app-layout>
