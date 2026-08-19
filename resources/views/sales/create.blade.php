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

            {{-- Mobile: card-per-product --}}
            <div class="sm:hidden space-y-2">
                @foreach ($products as $index => $product)
                    <div class="bg-white shadow rounded-xl p-4 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $product->name }}</p>
                            <p class="text-xs text-gray-500">{{ $product->quantity }} in stock</p>
                        </div>
                        <input type="hidden" name="sales[{{ $index }}][product_id]" value="{{ $product->id }}">
                        <input
                            type="number"
                            name="sales[{{ $index }}][quantity_sold]"
                            min="0"
                            max="{{ $product->quantity }}"
                            placeholder="0"
                            class="border rounded-lg p-2 text-sm w-20 text-center flex-shrink-0"
                        >
                    </div>
                @endforeach
            </div>

            {{-- Desktop: table --}}
            <table class="min-w-full text-sm mt-4 hidden sm:table bg-white shadow rounded">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="p-2">Product</th>
                        <th class="p-2">Current Stock</th>
                        <th class="p-2">Quantity Sold</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $index => $product)
                        <tr class="border-t">
                            <td class="p-2">{{ $product->name }}</td>
                            <td class="p-2 text-gray-600">{{ $product->quantity }}</td>
                            <td class="p-2">
                                <input type="number" name="sales[{{ $index }}][quantity_sold]" min="0" max="{{ $product->quantity }}" class="border rounded p-1 w-24">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="submit" class="mt-4 w-full sm:w-auto px-4 py-2.5 bg-indigo-600 text-white rounded-lg font-medium">Submit Sales</button>
        </form>
    </div>

    @include('partials.bottom-nav')
</x-app-layout>