<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Products</h2>
    </x-slot>
    
    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8">
        @if (auth()->user()->hasRole('approval_admin'))
    <a href="{{ route('users.index') }}" class="inline-block mb-4 ml-2 px-4 py-2 bg-gray-700 text-white rounded">
        Manage Users
    </a>
@endif

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        @auth
            @if (auth()->user()->hasRole('stock_admin'))
                <a href="{{ route('products.create') }}" class="inline-block mb-4 px-4 py-2 bg-indigo-600 text-white rounded">
                    + Add Product
                </a>
            @endif

            @if (auth()->user()->hasRole('approval_admin'))
                <a href="{{ route('stock-requests.index') }}" class="inline-block mb-4 ml-2 px-4 py-2 bg-yellow-600 text-white rounded">
                    Pending Stock Requests
                </a>
            @endif
        @endauth

        @if (auth()->user()->hasRole('sales_rep'))
    <a href="{{ route('sales.create') }}" class="inline-block mb-4 px-4 py-2 bg-indigo-600 text-white rounded">
        Log Today's Sales
    </a>
@endif

@if (auth()->user()->hasRole('stock_admin'))
    <a href="{{ route('sales.index') }}" class="inline-block mb-4 ml-2 px-4 py-2 bg-green-600 text-white rounded">
        Sales Report
    </a>
@endif

        <div class="bg-white shadow rounded overflow-x-auto">
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
                                    <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:underline">Edit</a>

                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Delete this product?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>

                                    <button type="button" onclick="document.getElementById('stock-form-{{ $product->id }}').classList.toggle('hidden')" class="text-yellow-700 hover:underline">
                                        Request Stock Update
                                    </button>

                                    <div id="stock-form-{{ $product->id }}" class="hidden mt-2">
                                        <form action="{{ route('stock-requests.store', $product) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            <input type="number" name="requested_quantity" min="0" placeholder="New qty" class="border rounded px-2 py-1 w-24" required>
                                            <input type="text" name="reason" placeholder="Reason" class="border rounded px-2 py-1">
                                            <button type="submit" class="px-3 py-1 bg-indigo-600 text-white rounded text-xs">Submit</button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-3 text-center text-gray-500">No products yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>