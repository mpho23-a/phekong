<x-guest-layout>
    <h2 class="text-lg font-semibold text-gray-900 mb-1">Log in</h2>
    <p class="text-sm text-gray-500 mb-6">Enter your details to access your account.</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        {{-- Email --}}
        <div>
            <x-input-label for="email" value="Email" class="mb-1" />
            <x-text-input
                id="email"
                class="block mt-1 w-full rounded-lg border-gray-300 py-3 px-4 text-base focus:border-indigo-500 focus:ring-indigo-500"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div>
            <x-input-label for="password" value="Password" class="mb-1" />
            <x-text-input
                id="password"
                class="block mt-1 w-full rounded-lg border-gray-300 py-3 px-4 text-base focus:border-indigo-500 focus:ring-indigo-500"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Remember me --}}
        <div class="flex items-center justify-between">
            <label for="remember_me" class="flex items-center gap-2">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm text-gray-600">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:underline">
                    Forgot password?
                </a>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit" class="w-full py-3 bg-green-800 text-white rounded-lg font-medium text-base hover:bg-indigo-700 transition">
            Log in
        </button>
    </form>
</x-guest-layout>