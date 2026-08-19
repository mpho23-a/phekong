<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sales Dashboard</h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Stat cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white shadow rounded p-5">
                <p class="text-sm text-gray-500">Today's Revenue</p>
                <p class="text-2xl font-bold text-indigo-600 mt-1">R{{ number_format($todayStats['revenue'], 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $todayStats['units'] }} units · {{ $todayStats['transactions'] }} sales logged</p>
            </div>
            <div class="bg-white shadow rounded p-5">
                <p class="text-sm text-gray-500">This Week</p>
                <p class="text-2xl font-bold text-indigo-600 mt-1">R{{ number_format($weekStats['revenue'], 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $weekStats['units'] }} units · {{ $weekStats['transactions'] }} sales logged</p>
            </div>
            <div class="bg-white shadow rounded p-5">
                <p class="text-sm text-gray-500">This Month</p>
                <p class="text-2xl font-bold text-indigo-600 mt-1">R{{ number_format($monthStats['revenue'], 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $monthStats['units'] }} units · {{ $monthStats['transactions'] }} sales logged</p>
            </div>
            <div class="bg-white shadow rounded p-5">
                <p class="text-sm text-gray-500">This Year</p>
                <p class="text-2xl font-bold text-indigo-600 mt-1">R{{ number_format($yearStats['revenue'], 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $yearStats['units'] }} units · {{ $yearStats['transactions'] }} sales logged</p>
            </div>
        </div>

        {{-- Weekly chart --}}
        <div class="bg-white shadow rounded p-5 mb-6">
            <p class="text-sm font-medium text-gray-700 mb-3">Revenue — Last 7 Days</p>
            <canvas id="weeklyChart" height="90"></canvas>
        </div>

        {{-- Top products --}}
        <div class="bg-white shadow rounded overflow-x-auto">
            <p class="text-sm font-medium text-gray-700 p-4 pb-0">Top Products This Month</p>
            <table class="min-w-full text-sm mt-3">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="p-3">Product</th>
                        <th class="p-3">Units Sold</th>
                        <th class="p-3">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topProducts as $product)
                        <tr class="border-t">
                            <td class="p-3 font-medium">{{ $product['name'] }}</td>
                            <td class="p-3">{{ $product['units'] }}</td>
                            <td class="p-3">R{{ number_format($product['revenue'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-3 text-center text-gray-500">No sales this month yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <a href="{{ route('sales.index') }}" class="text-indigo-600 hover:underline text-sm">View full sales log →</a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('weeklyChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($last7Days->pluck('label')) !!},
                datasets: [{
                    label: 'Revenue (R)',
                    data: {!! json_encode($last7Days->pluck('revenue')) !!},
                    backgroundColor: 'rgba(79, 70, 229, 0.7)',
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: (val) => 'R' + val } }
                }
            }
        });
    </script>
</x-app-layout>