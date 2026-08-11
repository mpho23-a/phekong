<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pending Stock Requests</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white shadow rounded overflow-x-auto">
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
                            <td class="p-3 space-x-2 flex gap-2">
                                <form action="{{ route('stock-requests.approve', $req) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded text-xs cursor-pointe">Approve</button>
                                </form>
                                <form action="{{ route('stock-requests.reject', $req) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-xs cursor-pointe">Reject</button>
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
</x-app-layout>