<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pending Stock Requests</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 sm:pb-6">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        {{-- Mobile: card list --}}
        <div class="sm:hidden space-y-3">
            @forelse ($pending as $req)
                <div class="bg-white shadow rounded-xl p-4">
                    <p class="font-semibold text-gray-900">{{ $req->product->name }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-sm text-gray-500">{{ $req->current_quantity }}</span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                        <span class="text-sm font-semibold text-indigo-600">{{ $req->requested_quantity }}</span>
                    </div>
                    @if ($req->reason)
                        <p class="text-xs text-gray-500 mt-1">"{{ $req->reason }}"</p>
                    @endif
                    <p class="text-[11px] text-gray-400 mt-2">
                        {{ $req->requester->name }} · {{ $req->created_at->format('d M Y, H:i') }}
                    </p>

                    <div class="flex gap-2 mt-3 pt-3 border-t border-gray-100">
                        <form action="{{ route('stock-requests.approve', $req) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-1 py-2 bg-green-600 text-white rounded-lg text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                Approve
                            </button>
                        </form>
                        <form action="{{ route('stock-requests.reject', $req) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-1 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Reject
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-6">No pending requests.</p>
            @endforelse
        </div>

        {{-- Desktop: table --}}
        <div class="hidden sm:block bg-white shadow rounded overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="p-3">Product</th>
                        <th class="p-3">Current</th>
                        <th class="p-3">Requested</th>
                        <th class="p-3">Reason</th>
                        <th class="p-3">Requested By</th>
                        <th class="p-3">Requested At</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pending as $req)
                        <tr class="border-t">
                            <td class="p-3 font-medium">{{ $req->product->name }}</td>
                            <td class="p-3">{{ $req->current_quantity }}</td>
                            <td class="p-3">{{ $req->requested_quantity }}</td>
                            <td class="p-3 text-gray-600">{{ $req->reason ?? '—' }}</td>
                            <td class="p-3">{{ $req->requester->name }}</td>
                            <td class="p-3">{{ $req->created_at->format('d M Y, H:i') }}</td>
                            <td class="p-3 space-x-2">
                                <form action="{{ route('stock-requests.approve', $req) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded text-xs">Approve</button>
                                </form>
                                <form action="{{ route('stock-requests.reject', $req) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-xs">Reject</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-3 text-center text-gray-500">No pending requests.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('partials.bottom-nav')
</x-app-layout>