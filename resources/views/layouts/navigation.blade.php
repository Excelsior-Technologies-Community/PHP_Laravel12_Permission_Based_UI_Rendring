<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

        <div class="flex">

            <!-- Logo -->
            <div class="shrink-0 flex items-center">
                <a href="{{ route('dashboard') }}">
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                {{-- Dashboard --}}
                <x-nav-link
                    :href="route('dashboard')"
                    :active="request()->routeIs('dashboard')"
                >
                    {{ __('Dashboard') }}
                </x-nav-link>

                {{-- Products --}}
                @can('view products')
                    <x-nav-link
                        :href="route('products.index')"
                        :active="request()->routeIs('products.*')"
                    >
                        {{ __('Products') }}
                    </x-nav-link>
                @endcan

                {{-- Super Admin Navigation --}}
                @role('super-admin')

                    {{-- User Roles --}}
                    <x-nav-link
                        :href="route('user.roles')"
                        :active="request()->routeIs('user.roles*')"
                    >
                        {{ __('User Access') }}
                    </x-nav-link>

                    {{-- Permissions --}}
                    <x-nav-link
                        :href="route('permissions.index')"
                        :active="request()->routeIs('permissions.*')"
                    >
                        {{ __('Permissions') }}
                    </x-nav-link>

                    {{-- Access Activities --}}
                    <x-nav-link
                        :href="route('access.activities')"
                        :active="request()->routeIs('access.activities')"
                    >
                        {{ __('Access Logs') }}
                    </x-nav-link>

                @endrole

            </div>
        </div>

        <!-- Simulator & Settings Dropdowns -->
        <div class="hidden sm:flex sm:items-center sm:gap-3 sm:ms-6">

            <!-- Role Simulator Dropdown -->
            @if(auth()->user()->hasRole(['super-admin', 'admin']) || session()->has('simulated_role'))
                <x-dropdown align="right" width="60">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-indigo-200 text-xs font-semibold rounded-lg text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none transition shadow-sm">
                            <span class="text-sm">🎭</span>
                            <span>View UI As:</span>
                            <span class="font-bold uppercase font-mono px-1.5 py-0.5 rounded bg-indigo-600 text-white text-[10px]">
                                {{ auth()->user()->getActiveRoleName() }}
                            </span>
                            <svg class="fill-current h-3.5 w-3.5" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-100 text-[11px] font-bold uppercase tracking-wider text-gray-400 font-mono">
                            Select Role to Simulate
                        </div>

                        <form action="{{ route('simulator.switch') }}" method="POST">
                            @csrf
                            <button type="submit" name="role" value="super-admin" class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-indigo-50 hover:text-indigo-900 flex items-center justify-between {{ auth()->user()->getActiveRoleName() === 'super-admin' ? 'bg-indigo-50 font-bold text-indigo-600' : '' }}">
                                <span>⚡ Super Admin</span>
                                <span class="text-[10px] text-gray-400">All Perms</span>
                            </button>
                            <button type="submit" name="role" value="admin" class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-indigo-50 hover:text-indigo-900 flex items-center justify-between {{ auth()->user()->getActiveRoleName() === 'admin' ? 'bg-indigo-50 font-bold text-indigo-600' : '' }}">
                                <span>👑 Admin</span>
                                <span class="text-[10px] text-gray-400">CRUD + Costs</span>
                            </button>
                            <button type="submit" name="role" value="staff" class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-indigo-50 hover:text-indigo-900 flex items-center justify-between {{ auth()->user()->getActiveRoleName() === 'staff' ? 'bg-indigo-50 font-bold text-indigo-600' : '' }}">
                                <span>👤 Staff</span>
                                <span class="text-[10px] text-gray-400">No Delete / Cost</span>
                            </button>
                            <button type="submit" name="role" value="viewer" class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-indigo-50 hover:text-indigo-900 flex items-center justify-between {{ auth()->user()->getActiveRoleName() === 'viewer' ? 'bg-indigo-50 font-bold text-indigo-600' : '' }}">
                                <span>👁️ Viewer</span>
                                <span class="text-[10px] text-gray-400">Read-Only</span>
                            </button>
                            <button type="submit" name="role" value="normal-user" class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-indigo-50 hover:text-indigo-900 flex items-center justify-between {{ auth()->user()->getActiveRoleName() === 'normal-user' ? 'bg-indigo-50 font-bold text-indigo-600' : '' }}">
                                <span>🚫 Normal User</span>
                                <span class="text-[10px] text-gray-400">No Roles</span>
                            </button>
                        </form>

                        @if(session()->has('simulated_role'))
                            <div class="border-t border-gray-100">
                                <form action="{{ route('simulator.exit') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-xs text-rose-600 font-bold hover:bg-rose-50 flex items-center gap-1.5">
                                        <span>✕</span> Exit Preview Mode
                                    </button>
                                </form>
                            </div>
                        @endif
                    </x-slot>
                </x-dropdown>
            @endif

            <!-- Settings Dropdown -->
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                    >
                        <div>
                            {{ Auth::user()->name }}
                        </div>

                        <div class="ms-1">
                            <svg
                                class="fill-current h-4 w-4"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </button>
                </x-slot>

                <x-slot name="content">
                    {{-- Profile --}}
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-dropdown-link>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link
                            :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                        >
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>

        <!-- Hamburger -->
        <div class="-me-2 flex items-center sm:hidden">

            <button
                @click="open = ! open"
                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
            >

                <svg
                    class="h-6 w-6"
                    stroke="currentColor"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <path
                        :class="{ 'hidden': open, 'inline-flex': !open }"
                        class="inline-flex"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />

                    <path
                        :class="{ 'hidden': !open, 'inline-flex': open }"
                        class="hidden"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>

            </button>

        </div>

    </div>
</div>

<!-- Responsive Navigation Menu -->
<div
    :class="{ 'block': open, 'hidden': !open }"
    class="hidden sm:hidden"
>

    <div class="pt-2 pb-3 space-y-1">

        {{-- Dashboard --}}
        <x-responsive-nav-link
            :href="route('dashboard')"
            :active="request()->routeIs('dashboard')"
        >
            {{ __('Dashboard') }}
        </x-responsive-nav-link>

        {{-- Products --}}
        @can('view products')
            <x-responsive-nav-link
                :href="route('products.index')"
                :active="request()->routeIs('products.*')"
            >
                {{ __('Products') }}
            </x-responsive-nav-link>
        @endcan

        {{-- Super Admin --}}
        @role('super-admin')

            {{-- User Roles --}}
            <x-responsive-nav-link
                :href="route('user.roles')"
                :active="request()->routeIs('user.roles*')"
            >
                {{ __('User Access') }}
            </x-responsive-nav-link>

            {{-- Permissions --}}
            <x-responsive-nav-link
                :href="route('permissions.index')"
                :active="request()->routeIs('permissions.*')"
            >
                {{ __('Permissions') }}
            </x-responsive-nav-link>

            {{-- Access Logs --}}
            <x-responsive-nav-link
                :href="route('access.activities')"
                :active="request()->routeIs('access.activities')"
            >
                {{ __('Access Logs') }}
            </x-responsive-nav-link>

        @endrole

    </div>

    <!-- Responsive Settings Options -->
    <div class="pt-4 pb-1 border-t border-gray-200">

        <div class="px-4">

            <div class="font-medium text-base text-gray-800">
                {{ Auth::user()->name }}
            </div>

            <div class="font-medium text-sm text-gray-500">
                {{ Auth::user()->email }}
            </div>

        </div>

        <div class="mt-3 space-y-1">

            {{-- Profile --}}
            <x-responsive-nav-link :href="route('profile.edit')">
                {{ __('Profile') }}
            </x-responsive-nav-link>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <x-responsive-nav-link
                    :href="route('logout')"
                    onclick="event.preventDefault(); this.closest('form').submit();"
                >
                    {{ __('Log Out') }}
                </x-responsive-nav-link>
            </form>

        </div>

    </div>

</div>


</nav>
