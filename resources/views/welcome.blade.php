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
</head>

<body class="min-h-screen bg-frappe-crust font-sans text-frappe-text antialiased">
    <div class="flex flex-col min-h-screen">
        <!-- Header -->
        <header class="bg-frappe-base shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <div class="text-2xl font-bold text-frappe-blue">
                    {{ config('app.name', 'Booking System') }}
                </div>
                <div>
                    <a href="{{ route('login') }}"
                        class="bg-frappe-blue text-white px-4 py-2 rounded hover:bg-frappe-sapphire transition">
                        {{ __('Sign In') }}
                    </a>
                </div>
            </div>
        </header>

        <section class="bg-frappe-mantle py-12 flex-1">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold text-frappe-lavender mb-6">{{ __('Browse Businesses') }}</h2>
                <div class="bg-frappe-surface0 rounded-lg shadow p-6">
                    <ul>
                        @forelse($businesses as $business)
                            <li class="mb-4">
                                <a href="{{ route('businesses.show', $business->id) }}"
                                    class="text-frappe-blue hover:underline text-lg font-semibold">
                                    {{ $business->name }}
                                </a>
                                <div class="text-frappe-subtext1">{{ $business->address }}</div>
                            </li>
                        @empty
                            <li>{{ __('No businesses found.') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </section>


    </div>
</body>

</html>
