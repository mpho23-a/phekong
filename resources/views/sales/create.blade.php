<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Log Today's Sales</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif
        @error('sales')
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ $message }}</div>
        @enderror

        <form action="{{ route('sales.store') }}" method="POST" class="bg-white shadow rounded p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium">Date</label>
                <input type="date" name="sale_date" value="{{ old('sale_date', now()->toDateString()) }}" class="mt-1 border rounded p-2">
            </div>

            <table class="min-w-full text-sm mt-4">
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
                                <input type="hidden" name="sales[{{ $index }}][product_id]" value="{{ $product->id }}">
                                <input type="number" name="sales[{{ $index }}][quantity_sold]" min="0" max="{{ $product->quantity }}" class="border rounded p-1 w-24">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="submit" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded">Submit Sales</button>
        </form>
    </div>
</x-app-layout>