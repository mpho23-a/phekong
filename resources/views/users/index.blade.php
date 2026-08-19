<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Users</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 sm:pb-6">

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif

        <a href="{{ route('users.create') }}" class="inline-block mb-4 px-4 py-2 bg-indigo-600 text-white rounded text-sm">
            + Add User
        </a>

        {{-- Mobile: card list --}}
        <div class="sm:hidden space-y-3">
            @forelse ($users as $user)
                <div class="bg-white shadow rounded-xl p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-900 truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                        </div>
                        @if ($user->id !== auth()->id())
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <a href="{{ route('users.edit', $user) }}" class="text-indigo-600" aria-label="Edit roles">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5v4.75A2.25 2.25 0 0117.25 20.5H5.75A2.25 2.25 0 013.5 18.25V6.75A2.25 2.25 0 015.75 4.5h4.75" />
                                    </svg>
                                </a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Remove this user?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600" aria-label="Remove user">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @else
                            <span class="text-xs text-gray-400 flex-shrink-0">You</span>
                        @endif
                    </div>
                    <div class="mt-2">
                        <span class="px-2 py-0.5 text-[11px] font-medium bg-indigo-100 text-indigo-700 rounded-full">
                            {{ $user->roles->pluck('name')->map(fn($r) => str_replace('_', ' ', $r))->join(', ') ?: 'No role' }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-6">No users found.</p>
            @endforelse
        </div>

        {{-- Desktop: table --}}
        <div class="hidden sm:block bg-white shadow rounded overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="p-3">Name</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Role</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="border-t">
                            <td class="p-3 font-medium">{{ $user->name }}</td>
                            <td class="p-3">{{ $user->email }}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 text-xs bg-indigo-100 text-indigo-700 rounded">
                                    {{ $user->roles->pluck('name')->map(fn($r) => str_replace('_', ' ', $r))->join(', ') ?: 'No role' }}
                                </span>
                            </td>
                            <td class="p-3 space-x-2">
                                @if ($user->id !== auth()->id())
                                    <a href="{{ route('users.edit', $user) }}" class="text-indigo-600 hover:underline">Edit Roles</a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Remove this user?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Remove</button>
                                    </form>
                                @else
                                    <span class="text-gray-400">You</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-3 text-center text-gray-500">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('partials.bottom-nav')
</x-app-layout>