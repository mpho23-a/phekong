<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sales Dashboard</h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 sm:pb-6">

        {{-- Hero: today's revenue, big number, Instagram-profile-stat energy --}}
        <div class="bg-white shadow rounded-2xl p-6 mb-5">
            <p class="text-xs uppercase tracking-wider text-gray-400 font-medium">Today's Revenue</p>
            <p class="text-4xl sm:text-5xl font-extrabold text-gray-900 tracking-tight mt-1">
                R{{ number_format($todayStats['revenue'], 2) }}
            </p>
            <p class="text-sm text-gray-500 mt-2">
                {{ $todayStats['units'] }} units sold · {{ $todayStats['transactions'] }} sales logged today
            </p>
        </div>

        {{-- Horizontal-scrolling stat strip — Instagram stories row style --}}
        <div class="flex gap-3 overflow-x-auto pb-2 mb-6 -mx-4 px-4 sm:mx-0 sm:px-0 scrollbar-hide">
            <div class="flex-shrink-0 w-32 bg-white shadow rounded-xl p-4 border-2 border-indigo-100">
                <p class="text-[11px] uppercase tracking-wide text-gray-400 font-medium">Today</p>
                <p class="text-lg font-bold text-gray-900 mt-1">R{{ number_format($todayStats['revenue'], 0) }}</p>
                <p class="text-[11px] text-gray-400">{{ $todayStats['transactions'] }} sales</p>
            </div>
            <div class="flex-shrink-0 w-32 bg-white shadow rounded-xl p-4 border-2 border-transparent">
                <p class="text-[11px] uppercase tracking-wide text-gray-400 font-medium">This Week</p>
                <p class="text-lg font-bold text-gray-900 mt-1">R{{ number_format($weekStats['revenue'], 0) }}</p>
                <p class="text-[11px] text-gray-400">{{ $weekStats['transactions'] }} sales</p>
            </div>
            <div class="flex-shrink-0 w-32 bg-white shadow rounded-xl p-4 border-2 border-transparent">
                <p class="text-[11px] uppercase tracking-wide text-gray-400 font-medium">This Month</p>
                <p class="text-lg font-bold text-gray-900 mt-1">R{{ number_format($monthStats['revenue'], 0) }}</p>
                <p class="text-[11px] text-gray-400">{{ $monthStats['transactions'] }} sales</p>
            </div>
            <div class="flex-shrink-0 w-32 bg-white shadow rounded-xl p-4 border-2 border-transparent">
                <p class="text-[11px] uppercase tracking-wide text-gray-400 font-medium">This Year</p>
                <p class="text-lg font-bold text-gray-900 mt-1">R{{ number_format($yearStats['revenue'], 0) }}</p>
                <p class="text-[11px] text-gray-400">{{ $yearStats['transactions'] }} sales</p>
            </div>
        </div>

        {{-- Weekly chart --}}
        <div class="bg-white shadow rounded-2xl p-5 mb-6">
            <p class="text-sm font-semibold text-gray-700 mb-3">Revenue — Last 7 Days</p>
            <canvas id="weeklyChart" height="140"></canvas>
        </div>

        {{-- Top products — card list on mobile, table on desktop --}}
        <div class="bg-white shadow rounded-2xl overflow-hidden">
            <p class="text-sm font-semibold text-gray-700 p-4 pb-2">Top Products This Month</p>

            {{-- Mobile: Instagram-post-style card list --}}
            <div class="sm:hidden divide-y divide-gray-100">
                @forelse ($topProducts as $product)
                    <div class="flex items-center gap-3 p-4">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-indigo-600 font-bold text-sm">{{ substr($product['name'], 0, 1) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $product['name'] }}</p>
                            <p class="text-xs text-gray-400">{{ $product['units'] }} units sold</p>
                        </div>
                        <p class="font-semibold text-gray-900 text-sm">R{{ number_format($product['revenue'], 0) }}</p>
                    </div>
                @empty
                    <p class="p-4 text-center text-gray-500 text-sm">No sales this month yet.</p>
                @endforelse
            </div>

            {{-- Desktop: table --}}
            <table class="min-w-full text-sm hidden sm:table">
                <thead class="bg-gray-50 text-left">
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

        <div class="mt-4 text-center sm:text-left">
            <a href="{{ route('sales.index') }}" class="text-indigo-600 hover:underline text-sm font-medium">View full sales log →</a>
        </div>
    </div>

    @include('partials.bottom-nav')

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

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
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderRadius: 6,
                    maxBarThickness: 40,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: (val) => 'R' + val } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
</x-app-layout>