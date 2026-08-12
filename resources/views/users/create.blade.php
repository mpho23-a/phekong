<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add User</h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto sm:px-6 lg:px-8">
        <form action="{{ route('users.store') }}" method="POST" class="bg-white shadow rounded p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full border rounded p-2">
                @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full border rounded p-2">
                @error('email') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Password</label>
                <input type="password" name="password" class="mt-1 block w-full border rounded p-2">
                @error('password') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Confirm Password</label>
                <input type="password" name="password_confirmation" class="mt-1 block w-full border rounded p-2">
            </div>

            <div>
                <label class="block text-sm font-medium">Role</label>
                <select name="role" class="mt-1 block w-full border rounded p-2">
                    <option value="">Select a role</option>
                    <option value="sales_rep" @selected(old('role') === 'sales_rep')>Sales Rep</option>
                    <option value="stock_admin" @selected(old('role') === 'stock_admin')>Stock Admin</option>
                    <option value="approval_admin" @selected(old('role') === 'approval_admin')>Approval Admin</option>
                </select>
                @error('role') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Add User</button>
                <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>