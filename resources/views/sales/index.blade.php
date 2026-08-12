<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sales Report</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">

        <form method="GET" class="flex flex-wrap gap-2 mb-4 bg-white p-4 rounded shadow">
            <select name="product_id" class="border rounded p-2">
                <option value="">All Products</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>{{ $product->name }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ request('from') }}" class="border rounded p-2">
            <input type="date" name="to" value="{{ request('to') }}" class="border rounded p-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Filter</button>
        </form>

        <div class="bg-white shadow rounded overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="p-3">Date</th>
                        <th class="p-3">Product</th>
                        <th class="p-3">Quantity Sold</th>
                        <th class="p-3">Sales Rep</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr class="border-t">
                            <td class="p-3">{{ $sale->sale_date->format('d M Y') }}</td>
                            <td class="p-3">{{ $sale->product->name }}</td>
                            <td class="p-3">{{ $sale->quantity_sold }}</td>
                            <td class="p-3">{{ $sale->salesRep->name }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-3 text-center text-gray-500">No sales recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $sales->links() }}</div>
    </div>
</x-app-layout>