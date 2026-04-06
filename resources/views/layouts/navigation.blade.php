<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    @php
        $user = Auth::user();
        $role = $user->role;
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link
                        :href="route('dashboard')"
                        :active="request()->routeIs('dashboard')"
                    >
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if ($role === 'Passenger')
                        <x-nav-link
                            :href="route('passenger.report')"
                            :active="request()->routeIs('passenger.report')"
                        >
                            {{ __('Report Lost Item') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('passenger.found_items')"
                            :active="request()->routeIs('passenger.found_items')"
                        >
                            {{ __('Browse Found Items') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('passenger.rewards')"
                            :active="request()->routeIs('passenger.rewards')"
                        >
                            {{ __('My Rewards') }}
                        </x-nav-link>
                    @endif

                    @if ($role === 'Admin')
                        <x-nav-link
                            :href="route('admin.staff.index')"
                            :active="request()->routeIs('admin.staff.*')"
                        >
                            {{ __('Staff Management') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('admin.logs.index')"
                            :active="request()->routeIs('admin.logs.*')"
                        >
                            {{ __('Audit Logs') }}
                        </x-nav-link>
                    @endif

                    @if ($role === 'Staff' || $role === 'Admin')
                        <x-nav-link
                            :href="$role === 'Admin' ? route('admin.dashboard') : route('staff.dashboard')"
                            :active="request()->routeIs('admin.dashboard') || request()->routeIs('staff.dashboard')"
                        >
                            {{ __('Analytics Chart') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('staff.vouchers.index')"
                            :active="request()->routeIs('staff.vouchers.*')"
                        >
                            {{ __('Vouchers') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('staff.found-items.index')"
                            :active="request()->routeIs('staff.found-items.*')"
                        >
                            {{ __('Found Items') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('staff.lost-items.index')"
                            :active="request()->routeIs('staff.lost-items.*')"
                            class="text-red-600 font-bold"
                        >
                            {{ __('Lost Reports') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('staff.claims.index')"
                            :active="request()->routeIs('staff.claims.*')"
                        >
                            {{ __('Claim History') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('staff.inventory.index')"
                            :active="request()->routeIs('staff.inventory.*')"
                        >
                            {{ __('Inventory') }}
                        </x-nav-link>

                        <x-nav-link
                            :href="route('staff.ai-chat.index')"
                            :active="request()->routeIs('staff.ai-chat.*')"
                        >
                            {{ __('AI Help Assistant') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ $user->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
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
                        <x-dropdown-link :href="route('profile.edit')">
                            @if ($role === 'Passenger')
                                {{ __('Passenger Profile') }}
                            @else
                                {{ __('Staff Profile') }}
                            @endif
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="w-full text-left block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none transition"
                            >
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button
                    @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                >
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
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

    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link
                :href="route('dashboard')"
                :active="request()->routeIs('dashboard')"
            >
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if ($role === 'Passenger')
                <x-responsive-nav-link
                    :href="route('passenger.report')"
                    :active="request()->routeIs('passenger.report')"
                >
                    {{ __('Report Lost Item') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('passenger.found_items')"
                    :active="request()->routeIs('passenger.found_items')"
                >
                    {{ __('Browse Found Items') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('passenger.rewards')"
                    :active="request()->routeIs('passenger.rewards')"
                >
                    {{ __('My Rewards') }}
                </x-responsive-nav-link>
            @endif

            @if ($role === 'Admin')
                <x-responsive-nav-link
                    :href="route('admin.staff.index')"
                    :active="request()->routeIs('admin.staff.*')"
                >
                    {{ __('Staff Management') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('admin.logs.index')"
                    :active="request()->routeIs('admin.logs.*')"
                >
                    {{ __('Audit Logs') }}
                </x-responsive-nav-link>
            @endif

            @if ($role === 'Staff' || $role === 'Admin')
                <x-responsive-nav-link
                    :href="$role === 'Admin' ? route('admin.dashboard') : route('staff.dashboard')"
                    :active="request()->routeIs('admin.dashboard') || request()->routeIs('staff.dashboard')"
                >
                    {{ __('Analytics Chart') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('staff.vouchers.index')"
                    :active="request()->routeIs('staff.vouchers.*')"
                >
                    {{ __('Vouchers') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('staff.found-items.index')"
                    :active="request()->routeIs('staff.found-items.*')"
                >
                    {{ __('Found Items') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('staff.lost-items.index')"
                    :active="request()->routeIs('staff.lost-items.*')"
                >
                    {{ __('Lost Reports') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('staff.claims.index')"
                    :active="request()->routeIs('staff.claims.*')"
                >
                    {{ __('Claim History') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('staff.inventory.index')"
                    :active="request()->routeIs('staff.inventory.*')"
                >
                    {{ __('Inventory') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('staff.ai-chat.index')"
                    :active="request()->routeIs('staff.ai-chat.*')"
                >
                    {{ __('AI Help Assistant') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ $user->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ $user->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    @if ($role === 'Passenger')
                        {{ __('Passenger Profile') }}
                    @else
                        {{ __('Staff Profile') }}
                    @endif
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full text-left block px-4 py-2 text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 focus:outline-none transition"
                    >
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>