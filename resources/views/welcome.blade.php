<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BookingSystem') }}</title>

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

        /* Navigation specific styles */
        .nav-container {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            z-index: 9999 !important;
            backdrop-filter: blur(15px) !important;
            transform: translateZ(0) !important;
        }

        nav.nav-container,
        .nav-container {
            position: fixed !important;
            top: 0px !important;
            left: 0px !important;
            right: 0px !important;
            width: 100vw !important;
            z-index: 99999 !important;
            transform: translateZ(0) !important;
            will-change: transform !important;
        }

        .nav-frosted {
            background: rgba(49, 50, 68, 0.8) !important;
            backdrop-filter: blur(15px) !important;
            border-bottom: 1px solid rgba(186, 194, 222, 0.15) !important;
            position: relative !important;
            z-index: 100 !important;
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

        .frosted-button {
            background: linear-gradient(to right, rgba(137, 180, 250, 0.20), rgba(99, 102, 241, 0.70)) !important;
            backdrop-filter: blur(6px) !important;
            border: 1px solid rgba(137, 180, 250, 0.70) !important;
            color: #89b4fa !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem !important;
            font-size: 0.875rem !important;
            transition: all 0.3s ease !important;
        }

        .frosted-button:hover {
            background: linear-gradient(to right, rgba(137, 180, 250, 0.30), rgba(99, 102, 241, 0.80)) !important;
        }

        .frosted-button-login {
            background: linear-gradient(to right, rgba(137, 180, 250, 0.16), rgba(99, 102, 241, 0.16)) !important;
            backdrop-filter: blur(6px) !important;
            border: 1px solid rgba(137, 180, 250, 0.24) !important;
            color: #cdd6f4 !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem !important;
            font-size: 0.875rem !important;
            transition: all 0.3s ease !important;
        }

        .frosted-button-login:hover {
            background: linear-gradient(to right, rgba(137, 180, 250, 0.35), rgba(99, 102, 241, 0.35)) !important;
        }

        .frosted-button-register {
            background: linear-gradient(to right, rgba(166, 209, 137, 0.16), rgba(148, 198, 123, 0.16)) !important;
            backdrop-filter: blur(6px) !important;
            border: 1px solid rgba(166, 209, 137, 0.24) !important;
            color: #a6d189 !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem !important;
            font-size: 0.875rem !important;
            transition: all 0.3s ease !important;
        }

        .frosted-button-register:hover {
            background: linear-gradient(to right, rgba(166, 209, 137, 0.35), rgba(148, 198, 123, 0.35)) !important;
        }

        .content-layer {
            position: relative;
            z-index: 10;
        }

        .hero-section {
            position: relative;
            z-index: 1;
            margin-top: 64px !important;
            padding-top: 48px !important;
        }

        /* Language switcher needs higher z-index */
        .language-switcher {
            position: relative;
            z-index: 1000;
        }

        /* Dropdown menu needs high z-index */
        .dropdown-menu {
            z-index: 1001 !important;
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
        @include('layouts.navigation')

        <!-- Hero Welcome Card -->
        <section class="py-12 hero-section">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Main Hero Card -->
                <div class="frosted-card overflow-hidden shadow-2xl sm:rounded-2xl border border-frappe-surface2 mb-8">
                    <div class="p-8 lg:p-12">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                            <!-- Left Content -->
                            <div>
                                <h1 class="text-4xl lg:text-5xl font-bold text-frappe-lavender mb-6 leading-tight">
                                    {{ __('messages.welcome_to_booking_system') }}
                                </h1>

                                <p class="text-lg text-frappe-text mb-6 leading-relaxed">
                                    {{ __('messages.hero_description') }}
                                </p>
                            </div>

                            <!-- Right Content - Action Buttons -->
                            <div class="flex flex-col items-center lg:items-end space-y-4">
                                <div class="w-full max-w-sm">
                                    <a href="{{ route('register') }}"
                                        class="w-full inline-flex items-center justify-center gap-3 bg-gradient-to-r from-green-500/20 to-emerald-500/20 backdrop-blur-sm border border-green-400/30 text-green-300 px-8 py-4 rounded-lg text-lg font-semibold hover:from-green-500/30 hover:to-emerald-500/30 transition-all">
                                        <x-heroicon-o-user-plus class="w-6 h-6" />
                                        {{ __('messages.register_now') }}
                                    </a>
                                </div>

                                <div class="w-full max-w-sm">
                                    <a href="{{ route('businesses.public.index') }}"
                                        class="w-full inline-flex items-center justify-center gap-3 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-300 px-8 py-4 rounded-lg text-lg font-semibold hover:from-blue-500/30 hover:to-indigo-500/30 transition-all">
                                        <x-heroicon-o-magnifying-glass class="w-6 h-6" />
                                        {{ __('messages.search_businesses') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features Card -->
                <div class="frosted-card overflow-hidden shadow-xl sm:rounded-2xl border border-frappe-surface2 mb-8">
                    <div class="p-8 lg:p-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- For Users -->
                            <div class="text-center md:text-left">
                                <div class="flex items-center justify-center md:justify-start gap-3 mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 rounded-full flex items-center justify-center">
                                        <x-heroicon-o-user class="w-6 h-6 text-frappe-blue" />
                                    </div>
                                    <h3 class="text-xl font-semibold text-frappe-blue">
                                        {{ __('messages.for_users') }}
                                    </h3>
                                </div>
                                <ul class="space-y-3 text-frappe-subtext0">
                                    <li class="flex items-start gap-3">
                                        <x-heroicon-o-check-circle class="w-5 h-5 text-green-400 mt-0.5 flex-shrink-0" />
                                        <span>{{ __('messages.user_feature_1') }}</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <x-heroicon-o-check-circle class="w-5 h-5 text-green-400 mt-0.5 flex-shrink-0" />
                                        <span>{{ __('messages.user_feature_2') }}</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <x-heroicon-o-check-circle class="w-5 h-5 text-green-400 mt-0.5 flex-shrink-0" />
                                        <span>{{ __('messages.user_feature_3') }}</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- For Business Owners -->
                            <div class="text-center md:text-left">
                                <div class="flex items-center justify-center md:justify-start gap-3 mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500/20 to-pink-500/20 rounded-full flex items-center justify-center">
                                        <x-heroicon-o-building-storefront class="w-6 h-6 text-frappe-mauve" />
                                    </div>
                                    <h3 class="text-xl font-semibold text-frappe-mauve">
                                        {{ __('messages.for_business_owners') }}
                                    </h3>
                                </div>
                                <ul class="space-y-3 text-frappe-subtext0">
                                    <li class="flex items-start gap-3">
                                        <x-heroicon-o-check-circle class="w-5 h-5 text-green-400 mt-0.5 flex-shrink-0" />
                                        <span>{{ __('messages.business_feature_1') }}</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <x-heroicon-o-check-circle class="w-5 h-5 text-green-400 mt-0.5 flex-shrink-0" />
                                        <span>{{ __('messages.business_feature_2') }}</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <x-heroicon-o-check-circle class="w-5 h-5 text-green-400 mt-0.5 flex-shrink-0" />
                                        <span>{{ __('messages.business_feature_3') }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="pt-0 pb-6 flex-1">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-frappe-lavender mb-8 text-center">
                    {{ __('messages.available_businesses') }}</h2>

                @forelse($businesses as $business)
                    @if ($loop->first)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @endif

                    <x-business-card :business="$business" />

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
    </section>>


    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const savedLanguage = localStorage.getItem('language');
            const currentLanguage = '{{ app()->getLocale() }}';
            if (savedLanguage && savedLanguage !== currentLanguage) {
                window.location.href = '/locale/init/' + savedLanguage;
            }

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
