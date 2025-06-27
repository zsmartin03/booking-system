<nav x-data="{ open: false }" class="nav-container nav-frosted">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0">
                    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center">
                        <!-- Booking icon: calendar -->
                        <svg class="block h-9 w-auto text-frappe-lavender" fill="none" viewBox="0 0 40 40"
                            stroke="currentColor" stroke-width="1.5">
                            <rect x="6" y="10" width="28" height="22" rx="5" fill="rgba(139,92,246,0.10)"
                                stroke="currentColor" />
                            <rect x="6" y="10" width="28" height="5" rx="2" fill="currentColor"
                                class="fill-frappe-lavender" />
                            <circle cx="13" cy="16" r="1.8" fill="currentColor" />
                            <circle cx="20" cy="16" r="1.8" fill="currentColor" />
                            <circle cx="27" cy="16" r="1.8" fill="currentColor" />
                        </svg>
                        <span class="ml-2 text-xl font-semibold text-frappe-lavender hidden md:inline">
                            {{ config('app.name', 'BookingSystem') }}
                        </span>
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6 gap-4">
                <!-- Language Switcher -->
                <x-language-switcher />

                @auth
                    <x-dropdown align="right" width="48" contentClasses="py-1 frosted-card dropdown-menu">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150 focus:outline-none text-frappe-text hover:text-frappe-lavender hover:bg-frappe-surface1">
                                <div>{{ Auth::user()->name }}</div>
                                <svg class="ml-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('dashboard')" class="text-frappe-text hover:bg-frappe-surface1">
                                {{ __('messages.dashboard') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('profile.edit')" class="text-frappe-text hover:bg-frappe-surface1">
                                {{ __('messages.profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="text-frappe-red hover:bg-frappe-surface1">
                                    {{ __('messages.logout') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center gap-4">
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150 focus:outline-none text-frappe-text hover:text-frappe-lavender hover:bg-frappe-surface1">
                            {{ __('messages.login') }}
                        </a>
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors duration-150 focus:outline-none bg-frappe-blue text-white hover:bg-frappe-sapphire">
                            {{ __('messages.register') }}
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-frappe-subtext1 hover:text-frappe-text hover:bg-frappe-surface1 focus:outline-none transition duration-150">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden nav-frosted">
        <!-- Language Switcher for Mobile -->
        <div class="pt-2 pb-3 border-b border-frappe-surface1">
            <div class="px-4">
                <x-language-switcher />
            </div>
        </div>

        @auth
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                    class="text-frappe-text hover:bg-frappe-surface1">
                    {{ __('messages.dashboard') }}
                </x-responsive-nav-link>
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-frappe-surface1">
                <div class="px-4">
                    <div class="font-medium text-base text-frappe-text">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-frappe-subtext1">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')" class="text-frappe-text hover:bg-frappe-surface1">
                        {{ __('messages.profile') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                            class="text-frappe-red hover:bg-frappe-surface1">
                            {{ __('messages.logout') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <!-- Guest responsive navigation -->
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('login')" class="text-frappe-text hover:bg-frappe-surface1">
                    {{ __('messages.login') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')" class="text-frappe-text hover:bg-frappe-surface1">
                    {{ __('messages.register') }}
                </x-responsive-nav-link>
            </div>
        @endauth
    </div>
</nav>
