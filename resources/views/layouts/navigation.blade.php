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

                    <!-- Notification Bell -->
                    <div class="relative flex items-center">
                        <button id="notification-bell" class="relative p-2 focus:outline-none" aria-label="Notifications">
                            <!-- Bell Icon -->
                            <x-heroicon-o-bell class="w-6 h-6 text-frappe-blue" />
                            <span id="notification-count"
                                class="absolute top-0 right-0 bg-frappe-red text-white text-xs rounded-full px-2 py-0.5 font-bold min-w-[1.25rem] text-center"
                                style="display:none;">0</span>
                        </button>
                        <!-- Dropdown -->
                        <div id="notification-dropdown"
                            class="hidden absolute right-0 mt-2 w-96 frosted-card rounded-xl shadow-2xl z-50 top-full">
                            <div class="p-4 border-b border-frappe-surface1 font-bold text-frappe-blue">
                                {{ __('messages.notifications') }}</div>
                            <ul id="notification-list" class="max-h-80 overflow-y-auto">
                                <li class="p-4 text-center text-frappe-subtpnext1">{{ __('messages.loading') }}...</li>
                            </ul>
                            <a href="{{ route('notifications.index') }}"
                                class="block text-center p-2 text-frappe-blue hover:underline">
                                {{ __('messages.view_all') }}
                            </a>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-4">
                        <a href="{{ route('login') }}"
                            class="frosted-button-login inline-flex items-center px-3 py-2 text-white rounded-lg transition-all focus:outline-none">
                            {{ __('messages.log_in') }}
                        </a>
                        <a href="{{ route('register') }}"
                            class="frosted-button-register inline-flex items-center px-3 py-2 text-white rounded-lg transition-all focus:outline-none">
                            {{ __('messages.register') }}
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Mobile Navigation Controls -->
            <div class="flex items-center gap-2 sm:hidden">
                @auth
                    <!-- Notification Bell for Mobile -->
                    <div class="relative">
                        <button id="notification-bell-mobile" class="relative p-2 focus:outline-none"
                            aria-label="Notifications">
                            <!-- Bell Icon -->
                            <x-heroicon-o-bell class="w-6 h-6 text-frappe-blue" />
                            <span id="notification-count-mobile"
                                class="absolute top-0 right-0 bg-frappe-red text-white text-xs rounded-full px-2 py-0.5 font-bold min-w-[1.25rem] text-center"
                                style="display:none;">0</span>
                        </button>
                        <!-- Dropdown -->
                        <div id="notification-dropdown-mobile"
                            class="hidden absolute right-0 mt-2 w-80 frosted-card rounded-xl shadow-2xl z-50 top-full">
                            <div class="p-4 border-b border-frappe-surface1 font-bold text-frappe-blue">
                                {{ __('messages.notifications') }}</div>
                            <ul id="notification-list-mobile" class="max-h-60 overflow-y-auto">
                                <li class="p-4 text-center text-frappe-subtpnext1">{{ __('messages.loading') }}...</li>
                            </ul>
                            <a href="{{ route('notifications.index') }}"
                                class="block text-center p-2 text-frappe-blue hover:underline">
                                {{ __('messages.view_all') }}
                            </a>
                        </div>
                    </div>
                @endauth

                <!-- Hamburger -->
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-frappe-text hover:text-frappe-text hover:bg-frappe-surface1 focus:outline-none transition duration-150">
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
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden nav">
        <!-- Language Switcher for Mobile -->
        <div class="pt-2 pb-3 border-b border-frappe-surface1">
            <div class="px-4 flex justify-end">
                <x-language-switcher alignment="right" />
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
            <div class="pb-1 border-t border-frappe-surface1">
                <div class="space-y-1">
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

@auth
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bell = document.getElementById('notification-bell');
            const dropdown = document.getElementById('notification-dropdown');
            const countSpan = document.getElementById('notification-count');
            const list = document.getElementById('notification-list');

            const bellMobile = document.getElementById('notification-bell-mobile');
            const dropdownMobile = document.getElementById('notification-dropdown-mobile');
            const countSpanMobile = document.getElementById('notification-count-mobile');
            const listMobile = document.getElementById('notification-list-mobile');

            let loaded = false;
            let loadedMobile = false;

            function fetchNotifications() {
                fetch("{{ route('notifications.index') }}")
                    .then(res => res.json())
                    .then(data => {
                        // Set count for both desktop and mobile
                        [countSpan, countSpanMobile].forEach(span => {
                            if (data.unreadCount > 0) {
                                span.textContent = data.unreadCount > 99 ? '99+' : data.unreadCount;
                                span.style.display = '';
                            } else {
                                span.style.display = 'none';
                            }
                        });

                        // Render notifications
                        const notificationHTML = data.notifications.length === 0 ?
                            `<li class='p-4 text-center text-frappe-subtpnext1'>{{ __('messages.no_notifications') }}</li>` :
                            data.notifications.map(n => `
                                <li class='px-4 py-3 border-b ${n.is_read ? 'bg-frappe-surface0/50 opacity-75' : 'bg-frappe-blue/10'}'>
                                    <div class='font-semibold text-frappe-blue'>${n.title}</div>
                                    <div class='text-frappe-text text-sm'>${n.content}</div>
                                    <div class='text-xs text-frappe-subtpnext1'>${n.sent_at ? new Date(n.sent_at).toLocaleString() : ''}</div>
                                </li>
                            `).join('');

                        list.innerHTML = notificationHTML;
                        listMobile.innerHTML = notificationHTML;
                    });
            }

            function handleBellClick(bell, dropdown, isLoaded) {
                return function(e) {
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                    if (!isLoaded) {
                        fetchNotifications();
                        isLoaded = true;
                        // Mark all as read
                        fetch("{{ route('notifications.markAllRead') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                        }).then(() => {
                            setTimeout(fetchNotifications, 500); // Refresh count
                        });
                    }
                };
            }

            bell.addEventListener('click', handleBellClick(bell, dropdown, loaded));
            bellMobile.addEventListener('click', handleBellClick(bellMobile, dropdownMobile, loadedMobile));

            // Hide dropdown on outside click
            document.addEventListener('click', function(e) {
                if (!dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                }
                if (!dropdownMobile.classList.contains('hidden')) {
                    dropdownMobile.classList.add('hidden');
                }
            });

            // Initial fetch for count
            fetchNotifications();
        });
    </script>
@endauth
