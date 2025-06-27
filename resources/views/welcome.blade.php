<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Booking System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html {
            overscroll-behavior: none;
        }

        .wave-container {
            position: relative;
            min-height: 100vh;
            background: linear-gradient(135deg, #8839ef, #7c3aed, #6366f1);
        }

        body {
            background: linear-gradient(135deg, #8839ef, #7c3aed, #6366f1);
            min-height: 100vh;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .wave-container::before {
            content: '';
            position: fixed;
            top: 50%;
            left: 50%;
            width: 200%;
            height: 400px;
            transform: translate(-50%, -50%) rotate(-15deg);
            border-radius: 100% 50%;
            background: rgba(137, 180, 250, 0.15);
            z-index: -1;
            pointer-events: none;
        }

        .frosted-glass {
            background: rgba(49, 50, 68, 0.35);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(186, 194, 222, 0.15);
            box-shadow: 0 8px 32px rgba(30, 30, 46, 0.2);
        }

        .nav-frosted {
            background: rgba(49, 50, 68, 0.8);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(186, 194, 222, 0.15);
        }

        .frosted-card {
            background: rgba(30, 30, 46, 0.7);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(186, 194, 222, 0.1);
            box-shadow: 0 12px 40px rgba(30, 30, 46, 0.3);
        }

        .frosted-button {
            background: linear-gradient(135deg, rgba(137, 180, 250, 0.25), rgba(137, 180, 250, 0.35));
            backdrop-filter: blur(8px);
            border: 1px solid rgba(137, 180, 250, 0.35);
            transition: all 0.3s ease;
        }

        .frosted-button:hover {
            background: linear-gradient(135deg, rgba(137, 180, 250, 0.4), rgba(137, 180, 250, 0.5));
        }

        .frosted-button-login {
            background: linear-gradient(135deg, rgba(137, 180, 250, 0.2), rgba(137, 180, 250, 0.3));
            backdrop-filter: blur(8px);
            border: 1px solid rgba(137, 180, 250, 0.3);
            color: white;
            transition: all 0.3s ease;
        }

        .frosted-button-login:hover {
            background: linear-gradient(135deg, rgba(137, 180, 250, 0.35), rgba(137, 180, 250, 0.45));
        }

        .frosted-button-register {
            background: linear-gradient(135deg, rgba(166, 209, 137, 0.25), rgba(166, 209, 137, 0.35));
            backdrop-filter: blur(8px);
            border: 1px solid rgba(166, 209, 137, 0.35);
            color: white;
            transition: all 0.3s ease;
        }

        .frosted-button-register:hover {
            background: linear-gradient(135deg, rgba(166, 209, 137, 0.4), rgba(166, 209, 137, 0.5));
        }

        .content-layer {
            position: relative;
            z-index: 10;
        }

        .hamburger-button {
            background: rgba(137, 180, 250, 0.2);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(137, 180, 250, 0.3);
            color: rgba(137, 180, 250, 1);
            transition: all 0.3s ease;
        }

        .hamburger-button:hover {
            background: rgba(137, 180, 250, 0.3);
            color: white;
        }

        .mobile-menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 50;
            backdrop-filter: blur(4px);
        }

        .mobile-menu-drawer {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 16rem;
            background: rgba(49, 50, 68, 0.95);
            backdrop-filter: blur(20px);
            border-left: 1px solid rgba(186, 194, 222, 0.15);
            z-index: 51;
        }
    </style>
</head>

<body class="min-h-screen bg-frappe-crust font-sans text-frappe-text antialiased wave-container">
    <div class="flex flex-col min-h-screen content-layer">
        <!-- Header -->
        <header class="nav-frosted shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <div class="text-2xl font-bold text-frappe-lavender">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <div class="shrink-0">
                            <a href="{{ auth()->check() ? route('dashboard') : route('home') }}"
                                class="flex items-center">
                                <!-- Booking icon: calendar -->
                                <svg class="block h-9 w-auto text-frappe-lavender" fill="none" viewBox="0 0 40 40"
                                    stroke="currentColor" stroke-width="1.5">
                                    <rect x="6" y="10" width="28" height="22" rx="5"
                                        fill="rgba(139,92,246,0.10)" stroke="currentColor" />
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
                </div>
                <!-- Desktop menu -->
                <div class="hidden sm:flex items-center gap-4">
                    <x-language-switcher />
                    <a href="{{ route('login') }}"
                        class="frosted-button-login text-white px-4 py-2 rounded-lg transition-all inline-flex items-center gap-2">
                        {{ __('messages.login') }}
                    </a>
                    <a href="{{ route('register') }}"
                        class="frosted-button-register text-white px-4 py-2 rounded-lg transition-all inline-flex items-center gap-2">
                        {{ __('messages.register') }}
                    </a>
                </div>
                <!-- Mobile hamburger -->
                <div class="sm:hidden flex items-center">
                    <button id="mobile-menu-toggle"
                        class="hamburger-button p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-frappe-blue"
                        aria-label="Open menu">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Mobile menu overlay -->
        <div id="mobile-menu" class="mobile-menu-overlay hidden sm:hidden">
            <div class="mobile-menu-drawer shadow-xl p-6 flex flex-col gap-4 animate-slide-in-right">
                <button id="mobile-menu-close"
                    class="self-end mb-4 p-2 rounded focus:outline-none focus:ring-2 focus:ring-frappe-blue text-frappe-lavender hover:text-white transition-colors"
                    aria-label="Close menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <x-language-switcher />
                <a href="{{ route('login') }}"
                    class="frosted-button-login text-white px-4 py-2 rounded-lg transition-all inline-flex items-center gap-2 w-full justify-center">
                    {{ __('messages.login') }}
                </a>
                <a href="{{ route('register') }}"
                    class="frosted-button-register text-white px-4 py-2 rounded-lg transition-all inline-flex items-center gap-2 w-full justify-center">
                    {{ __('messages.register') }}
                </a>
            </div>
        </div>

        <section class="py-12 flex-1">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-frappe-lavender mb-8 text-center">
                    {{ __('messages.available_businesses') }}</h2>

                @forelse($businesses as $business)
                    @if ($loop->first)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @endif

                    <div
                        class="frosted-card overflow-hidden shadow-lg sm:rounded-xl border border-frappe-surface2 hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:transform hover:-translate-y-2">
                        <div class="p-6">
                            <div class="mb-4">
                                <a href="{{ route('businesses.show', $business->id) }}"
                                    class="text-frappe-blue hover:text-frappe-sapphire text-xl font-semibold block mb-2 transition-colors">
                                    {{ $business->name }}
                                </a>
                                <p class="text-frappe-subtext1 text-sm opacity-80">{{ $business->address }}</p>
                                @if ($business->description)
                                    <p class="text-frappe-subtext0 text-sm mt-2 opacity-70">
                                        {{ $business->description }}</p>
                                @endif
                            </div>

                            <div class="flex justify-center">
                                <a href="{{ route('businesses.show', $business->id) }}"
                                    class="frosted-button text-white px-4 py-2 rounded-lg transition-all inline-flex items-center gap-2">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                    {{ __('messages.view') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    @if ($loop->last)
            </div>
            @endif
        @empty
            <div class="frosted-card rounded-xl shadow-lg p-8 text-center">
                <div class="text-frappe-subtext1 text-lg">{{ __('messages.no_businesses_found') }}</div>
                <div class="text-frappe-subtext0 mt-2">{{ __('messages.check_back_later') }}</div>
            </div>
            @endforelse
    </div>
    </section>


    </div>

    <script>
        // Initialize language from localStorage on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedLanguage = localStorage.getItem('language');
            const currentLanguage = '{{ app()->getLocale() }}';
            if (savedLanguage && savedLanguage !== currentLanguage) {
                window.location.href = '/locale/init/' + savedLanguage;
            }

            // Hamburger menu logic
            const menu = document.getElementById('mobile-menu');
            const openBtn = document.getElementById('mobile-menu-toggle');
            const closeBtn = document.getElementById('mobile-menu-close');
            if (openBtn && menu) {
                openBtn.addEventListener('click', () => {
                    menu.classList.remove('hidden');
                });
            }
            if (closeBtn && menu) {
                closeBtn.addEventListener('click', () => {
                    menu.classList.add('hidden');
                });
            }
            // Close on backdrop click
            if (menu) {
                menu.addEventListener('click', (e) => {
                    if (e.target === menu) menu.classList.add('hidden');
                });
            }
        });
    </script>
    <style>
        @keyframes slide-in-right {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .animate-slide-in-right {
            animation: slide-in-right 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</body>

</html>
