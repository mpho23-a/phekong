{{--
    Instagram-style bottom tab bar — mobile only.
    Role-aware: shows different icons depending on who's logged in.
    Include this in your main layout, right before </body> or at the end of the layout content.
--}}

@auth
<nav class="fixed bottom-0 inset-x-0 z-50 bg-white border-t border-gray-200 sm:hidden"
     style="padding-bottom: env(safe-area-inset-bottom, 0px);">
    <div class="flex items-center justify-around h-16 px-2">

        @if (auth()->user()->hasRole('stock_admin'))
            {{-- Dashboard --}}
            <a href="{{ route('sales.dashboard') }}" class="flex flex-col items-center justify-center flex-1 h-full">
                <svg class="w-6 h-6 {{ request()->routeIs('sales.dashboard') ? 'text-green-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('sales.dashboard') ? '2.5' : '2' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-[10px] mt-0.5 {{ request()->routeIs('sales.dashboard') ? 'text-green-600 font-medium' : 'text-gray-400' }}">Dashboard</span>
            </a>

            {{-- Products --}}
            <a href="{{ route('products.index') }}" class="flex flex-col items-center justify-center flex-1 h-full">
                <svg class="w-6 h-6 {{ request()->routeIs('products.index') ? 'text-green-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('products.index') ? '2.5' : '2' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span class="text-[10px] mt-0.5 {{ request()->routeIs('products.index') ? 'text-green-600 font-medium' : 'text-gray-400' }}">Products</span>
            </a>

            {{-- Elevated Add Sale button --}}
            <a href="{{ route('sales.create') }}" class="flex flex-col items-center justify-center flex-1 h-full">
                <span class="flex items-center justify-center w-11 h-11 rounded-full bg-green-600 -mt-5 shadow-lg shadow-green-600/30">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </span>
            </a>

            {{-- Sales Report --}}
            <a href="{{ route('sales.index') }}" class="flex flex-col items-center justify-center flex-1 h-full">
                <svg class="w-6 h-6 {{ request()->routeIs('sales.index') ? 'text-green-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('sales.index') ? '2.5' : '2' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V9m4 8V5m4 12v-6M4 19h16a1 1 0 001-1V6a1 1 0 00-1-1H4a1 1 0 00-1 1v12a1 1 0 001 1z" />
                </svg>
                <span class="text-[10px] mt-0.5 {{ request()->routeIs('sales.index') ? 'text-green-600 font-medium' : 'text-gray-400' }}">Reports</span>
            </a>

            {{-- Profile --}}
            <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center flex-1 h-full">
                <svg class="w-6 h-6 {{ request()->routeIs('profile.edit') ? 'text-green-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('profile.edit') ? '2.5' : '2' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                <span class="text-[10px] mt-0.5 {{ request()->routeIs('profile.edit') ? 'text-green-600 font-medium' : 'text-gray-400' }}">Profile</span>
            </a>
        @endif

        @if (auth()->user()->hasRole('sales_rep') && !auth()->user()->hasRole('stock_admin'))
            {{-- Products --}}
            <a href="{{ route('products.index') }}" class="flex flex-col items-center justify-center flex-1 h-full">
                <svg class="w-6 h-6 {{ request()->routeIs('products.index') ? 'text-green-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('products.index') ? '2.5' : '2' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span class="text-[10px] mt-0.5 {{ request()->routeIs('products.index') ? 'text-green-600 font-medium' : 'text-gray-400' }}">Products</span>
            </a>

            {{-- Elevated Log Sale button --}}
            <a href="{{ route('sales.create') }}" class="flex flex-col items-center justify-center flex-1 h-full">
                <span class="flex items-center justify-center w-11 h-11 rounded-full bg-green-600 -mt-5 shadow-lg shadow-green-600/30">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </span>
                <span class="text-[10px] mt-1 text-gray-400">Log Sale</span>
            </a>

            {{-- Profile --}}
            <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center flex-1 h-full">
                <svg class="w-6 h-6 {{ request()->routeIs('profile.edit') ? 'text-green-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('profile.edit') ? '2.5' : '2' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                <span class="text-[10px] mt-0.5 {{ request()->routeIs('profile.edit') ? 'text-green-600 font-medium' : 'text-gray-400' }}">Profile</span>
            </a>
        @endif

        @if (auth()->user()->hasRole('approval_admin'))
            {{-- Products --}}
            <a href="{{ route('products.index') }}" class="flex flex-col items-center justify-center flex-1 h-full">
                <svg class="w-6 h-6 {{ request()->routeIs('products.index') ? 'text-green-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('products.index') ? '2.5' : '2' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span class="text-[10px] mt-0.5 {{ request()->routeIs('products.index') ? 'text-green-600 font-medium' : 'text-gray-400' }}">Products</span>
            </a>

            {{-- Pending Requests --}}
            <a href="{{ route('stock-requests.index') }}" class="flex flex-col items-center justify-center flex-1 h-full">
                <svg class="w-6 h-6 {{ request()->routeIs('stock-requests.index') ? 'text-green-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('stock-requests.index') ? '2.5' : '2' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-[10px] mt-0.5 {{ request()->routeIs('stock-requests.index') ? 'text-green-600 font-medium' : 'text-gray-400' }}">Approvals</span>
            </a>

            {{-- Manage Users --}}
            <a href="{{ route('users.index') }}" class="flex flex-col items-center justify-center flex-1 h-full">
                <svg class="w-6 h-6 {{ request()->routeIs('users.*') ? 'text-green-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('users.*') ? '2.5' : '2' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                </svg>
                <span class="text-[10px] mt-0.5 {{ request()->routeIs('users.*') ? 'text-green-600 font-medium' : 'text-gray-400' }}">Users</span>
            </a>

            {{-- Profile --}}
            <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center flex-1 h-full">
                <svg class="w-6 h-6 {{ request()->routeIs('profile.edit') ? 'text-green-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('profile.edit') ? '2.5' : '2' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                <span class="text-[10px] mt-0.5 {{ request()->routeIs('profile.edit') ? 'text-green-600 font-medium' : 'text-gray-400' }}">Profile</span>
            </a>
        @endif

    </div>
</nav>
@endauth