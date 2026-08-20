<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add Product</h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto sm:px-6 lg:px-8">
        <form action="{{ route('products.store') }}" method="POST" class="bg-white shadow rounded p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter prodcut name" class="mt-1 block w-full border rounded p-2">
                @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Description</label>
                <textarea  placeholder="Enter product description" name="description" class="mt-1 block w-full border rounded p-2">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium">Price (R)</label>
                <input type="number" step="0.01" name="price" value="{{ old('price') }}" class="mt-1 block w-full border rounded p-2">
                @error('price') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Initial Quantity</label>
                <input type="number" name="quantity" value="{{ old('quantity', 0) }}" class="mt-1 block w-full border rounded p-2">
            </div>

            <div>
                <label class="block text-sm font-medium">Low Stock Threshold</label>
                <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', 10) }}" class="mt-1 block w-full border rounded p-2">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-phekong text-white rounded">Save</button>
                <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>