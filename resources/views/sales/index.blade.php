<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sales Report</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 sm:pb-6">

        {{-- Quick filters --}}
        <div class="flex flex-wrap gap-2 mb-4">
            <a href="{{ route('sales.dashboard') }}" class="px-3 py-1.5 bg-indigo-600 text-white rounded-full text-xs font-medium">Dashboard</a>
            <a href="{{ route('sales.index', ['from' => now()->toDateString(), 'to' => now()->toDateString()]) }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">Today</a>
            <a href="{{ route('sales.index', ['from' => now()->startOfWeek()->toDateString(), 'to' => now()->toDateString()]) }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">This Week</a>
            <a href="{{ route('sales.index', ['from' => now()->startOfMonth()->toDateString(), 'to' => now()->toDateString()]) }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">This Month</a>
            <a href="{{ route('sales.index', ['from' => now()->startOfYear()->toDateString(), 'to' => now()->toDateString()]) }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">This Year</a>
        </div>

        <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-2 mb-4 bg-white p-4 rounded-xl shadow">
            <select name="product_id" class="border rounded p-2 text-sm w-full sm:w-auto">
                <option value="">All Products</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>{{ $product->name }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ request('from') }}" class="border rounded p-2 text-sm w-full sm:w-auto">
            <input type="date" name="to" value="{{ request('to') }}" class="border rounded p-2 text-sm w-full sm:w-auto">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded text-sm w-full sm:w-auto">Filter</button>
        </form>

        {{-- Mobile: card list --}}
        <div class="sm:hidden space-y-2">
            @forelse ($sales as $sale)
                <div class="bg-white shadow rounded-xl p-4 flex items-center justify-between">
                    <div class="min-w-0">
                        <p class="font-medium text-gray-900 truncate">{{ $sale->product->name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $sale->sale_date->format('d M Y') }} · {{ $sale->salesRep->name }}
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0 ml-3">
                        <p class="font-semibold text-gray-900">{{ $sale->quantity_sold }} units</p>
                        <p class="text-xs text-green-600">R{{ number_format($sale->quantity_sold * $sale->price_at_sale, 2) }}</p>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-6">No sales recorded.</p>
            @endforelse
        </div>

        {{-- Desktop: table --}}
        <div class="hidden sm:block bg-white shadow rounded overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="p-3">Date</th>
                        <th class="p-3">Product</th>
                        <th class="p-3">Quantity Sold</th>
                        <th class="p-3">Revenue</th>
                        <th class="p-3">Sales Rep</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr class="border-t">
                            <td class="p-3">{{ $sale->sale_date->format('d M Y') }}</td>
                            <td class="p-3">{{ $sale->product->name }}</td>
                            <td class="p-3">{{ $sale->quantity_sold }}</td>
                            <td class="p-3">R{{ number_format($sale->quantity_sold * $sale->price_at_sale, 2) }}</td>
                            <td class="p-3">{{ $sale->salesRep->name }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-3 text-center text-gray-500">No sales recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $sales->links() }}</div>
    </div>

    @include('partials.bottom-nav')
</x-app-layout>