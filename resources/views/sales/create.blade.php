<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Log Today's Sales</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 sm:pb-6">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif
        @error('sales')
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ $message }}</div>
        @enderror

        <form action="{{ route('sales.store') }}" method="POST">
            @csrf

            <div class="bg-white shadow rounded-xl p-4 mb-4">
                <label class="block text-sm font-medium mb-1">Date</label>
                <input type="date" name="sale_date" value="{{ old('sale_date', now()->toDateString()) }}" class="border rounded p-2 text-sm w-full sm:w-auto">
            </div>

            {{-- Single row per product — one input, responsive layout, no duplication --}}
            <div class="bg-white shadow rounded-xl overflow-hidden divide-y divide-gray-100">
                <div class="hidden sm:flex bg-gray-100 text-left text-sm font-medium text-gray-600 px-4 py-2">
                    <span class="flex-1">Product</span>
                    <span class="w-28">Current Stock</span>
                    <span class="w-28">Qty Sold</span>
                </div>

                @foreach ($products as $index => $product)
                    <div class="flex items-center justify-between gap-3 px-4 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-gray-900 truncate">{{ $product->name }}</p>
                            <p class="text-xs text-gray-500 sm:hidden">{{ $product->quantity }} in stock</p>
                        </div>
                        <span class="hidden sm:block w-28 text-sm text-gray-600">{{ $product->quantity }}</span>

                        <input type="hidden" name="sales[{{ $index }}][product_id]" value="{{ $product->id }}">
                        <input
                            type="number"
                            name="sales[{{ $index }}][quantity_sold]"
                            min="0"
                            max="{{ $product->quantity }}"
                            placeholder="0"
                            class="border rounded-lg p-2 text-sm w-20 sm:w-24 text-center flex-shrink-0"
                        >
                    </div>
                @endforeach
            </div>

            <button type="submit" class="mt-4 w-full sm:w-auto px-4 py-2.5 bg-indigo-600 text-white rounded-lg font-medium">Submit Sales</button>
        </form>
    </div>

    @include('partials.bottom-nav')
</x-app-layout>