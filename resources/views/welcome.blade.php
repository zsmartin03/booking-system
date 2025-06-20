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
            min-height: 100vh;
            overflow-x: hidden;
            overflow-y: auto;
            position: relative;
        }

        .wave-container {
            position: relative;
            width: 100%;
            min-height: 100vh;
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
                <div>
                    <a href="{{ route('login') }}"
                        class="frosted-button text-white px-6 py-3 rounded-lg hover:transform hover:-translate-y-1 transition-all inline-flex items-center gap-2">
                        {{ __('Sign In') }}
                    </a>
                </div>
            </div>
        </header>

        <section class="py-12 flex-1">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-frappe-lavender mb-8 text-center">{{ __('Browse Businesses') }}</h2>
                <div class="frosted-card rounded-xl shadow-lg p-8">
                    <ul class="space-y-6">
                        @forelse($businesses as $business)
                            <li
                                class="p-4 bg-frappe-surface0/30 rounded-lg backdrop-blur-sm border border-frappe-surface1/20">
                                <a href="{{ route('businesses.show', $business->id) }}"
                                    class="text-frappe-blue hover:text-frappe-sapphire text-xl font-semibold transition-colors">
                                    {{ $business->name }}
                                </a>
                                <div class="text-frappe-subtext1 mt-2">{{ $business->address }}</div>
                                @if ($business->description)
                                    <div class="text-frappe-subtext0 mt-1 text-sm">{{ $business->description }}</div>
                                @endif
                            </li>
                        @empty
                            <li class="text-center py-8">
                                <div class="text-frappe-subtext1 text-lg">{{ __('No businesses found.') }}</div>
                                <div class="text-frappe-subtext0 mt-2">{{ __('Check back later for new listings!') }}
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </section>


    </div>
</body>

</html>
