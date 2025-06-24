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
            background: linear-gradient(135deg, #8839ef, #7c3aed, #6366f1);
            overscroll-behavior: none;
            background-attachment: fixed;
            overflow-x: hidden;
        }

        body {
            background: linear-gradient(135deg, #8839ef, #7c3aed, #6366f1);
            background-attachment: fixed;
            overflow-x: hidden;
            overflow-y: auto;
            position: relative;
        }

        .wave-container {
            position: relative;
            width: 100%;
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
            z-index: -2;
            pointer-events: none;
        }

        .wave-container::after {
            content: '';
            position: fixed;
            top: 50%;
            left: 50%;
            width: 180%;
            height: 350px;
            transform: translate(-50%, -50%) rotate(-15deg);
            border-radius: 100% 50%;
            background: rgba(166, 209, 137, 0.1);
            z-index: -1;
            pointer-events: none;
        }

        .frosted-glass {
            background: rgba(49, 50, 68, 0.35);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(186, 194, 222, 0.15);
            box-shadow: 0 8px 32px rgba(30, 30, 46, 0.2);
        }

        .frosted-card {
            background: rgba(30, 30, 46, 0.7);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(186, 194, 222, 0.1);
            box-shadow: 0 12px 40px rgba(30, 30, 46, 0.3);
        }

        .frosted-button {
            background: rgba(137, 180, 250, 0.25);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(137, 180, 250, 0.35);
            transition: all 0.3s ease;
            color: #cdd6f4;
        }

        .frosted-button:hover {
            background: rgba(137, 180, 250, 0.4);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(137, 180, 250, 0.15);
        }

        .content-layer {
            position: relative;
            z-index: 10;
        }
    </style>
</head>

<body class="min-h-screen font-sans text-frappe-text antialiased wave-container">
    <div class="flex flex-col min-h-screen content-layer">
        <!-- Header -->
        <header class="frosted-glass shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <div class="text-2xl font-bold text-frappe-lavender">
                    {{ config('app.name', 'Booking System') }}
                </div>
                <div class="flex items-center gap-4">
                    <!-- Language Switcher -->
                    <x-language-switcher />
                    <a href="{{ route('login') }}"
                        class="frosted-button text-white px-6 py-3 rounded-lg hover:transform hover:-translate-y-1 transition-all inline-flex items-center gap-2">
                        {{ __('messages.login') }}
                    </a>
                </div>
            </div>
        </header>

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
                                    class="frosted-button text-white px-4 py-2 rounded-lg hover:transform hover:-translate-y-1 transition-all inline-flex items-center gap-2">
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

            // If there's a saved language different from current, switch to it
            if (savedLanguage && savedLanguage !== currentLanguage) {
                // Redirect to set the locale in session
                window.location.href = '/locale/init/' + savedLanguage;
            }
        });
    </script>
</body>

</html>
