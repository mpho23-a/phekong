<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Roles — {{ $user->name }}</h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="{{ route('users.update', $user) }}" method="POST" class="bg-white shadow rounded p-6 space-y-4">
            @csrf @method('PUT')

            <div>
                <p class="text-sm text-gray-600 mb-2">{{ $user->email }}</p>
                <label class="block text-sm font-medium mb-2">Roles (select one or more)</label>

                @foreach ($allRoles as $role)
                    <label class="flex items-center gap-2 mb-2">
                        <input type="checkbox" name="roles[]" value="{{ $role }}"
                               @checked(in_array($role, $userRoles))
                               class="rounded border-gray-300">
                        <span class="capitalize">{{ str_replace('_', ' ', $role) }}</span>
                    </label>
                @endforeach

                @error('roles') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Save Roles</button>
                <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>