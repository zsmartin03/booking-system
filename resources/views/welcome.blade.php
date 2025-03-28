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
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-center items-center">
                    <div class="text-2xl font-bold text-frappe-blue">
                        {{ config('app.name', 'Booking System') }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Auth Forms Section -->
        <section id="auth-forms" class="flex-1 bg-frappe-mantle py-16">
            <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-frappe-base rounded-xl shadow-lg overflow-hidden">
                    <!-- Tab Navigation -->
                    <div class="flex">
                        <button id="login-tab" class="tab-button flex-1 py-4 px-6 text-center font-medium text-frappe-blue border-b-4 border-frappe-blue">
                            Login
                        </button>
                        <button id="register-tab" class="tab-button flex-1 py-4 px-6 text-center font-medium text-frappe-subtext1">
                            Register
                        </button>
                    </div>

                    <!-- Forms -->
                    <div class="p-6">
                        <!-- Login Form -->
                        <div id="login-form">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="space-y-6">
                                    <div>
                                        <x-input-label for="login-email" :value="__('Email')" class="text-frappe-text" />
                                        <x-text-input id="login-email" class="block mt-1 w-full bg-frappe-surface0 border-frappe-surface1 text-frappe-text" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-frappe-red" />
                                    </div>

                                    <div>
                                        <x-input-label for="login-password" :value="__('Password')" class="text-frappe-text" />
                                        <x-text-input id="login-password" class="block mt-1 w-full bg-frappe-surface0 border-frappe-surface1 text-frappe-text" type="password" name="password" required autocomplete="current-password" />
                                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-frappe-red" />
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <label for="remember_me" class="inline-flex items-center">
                                            <input id="remember_me" type="checkbox" class="rounded bg-frappe-surface0 border-frappe-surface1 text-frappe-blue focus:ring-frappe-blue" name="remember">
                                            <span class="ml-2 text-sm text-frappe-text">{{ __('Remember me') }}</span>
                                        </label>

                                        @if (Route::has('password.request'))
                                            <a class="text-sm text-frappe-blue hover:text-frappe-sapphire" href="{{ route('password.request') }}">
                                                {{ __('Forgot your password?') }}
                                            </a>
                                        @endif
                                    </div>

                                    <x-primary-button class="w-full justify-center bg-frappe-blue hover:bg-frappe-sapphire">
                                        {{ __('Log in') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>

                        <!-- Register Form (hidden by default) -->
                        <div id="register-form" class="hidden">
                            <form method="POST" action="{{ route('register') }}">
                                @csrf
                                <div class="space-y-6">
                                    <div>
                                        <x-input-label for="register-name" :value="__('Name')" class="text-frappe-text" />
                                        <x-text-input id="register-name" class="block mt-1 w-full bg-frappe-surface0 border-frappe-surface1 text-frappe-text" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-frappe-red" />
                                    </div>

                                    <div>
                                        <x-input-label for="register-email" :value="__('Email')" class="text-frappe-text" />
                                        <x-text-input id="register-email" class="block mt-1 w-full bg-frappe-surface0 border-frappe-surface1 text-frappe-text" type="email" name="email" :value="old('email')" required autocomplete="username" />
                                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-frappe-red" />
                                    </div>

                                    <div>
                                        <x-input-label for="register-password" :value="__('Password')" class="text-frappe-text" />
                                        <x-text-input id="register-password" class="block mt-1 w-full bg-frappe-surface0 border-frappe-surface1 text-frappe-text" type="password" name="password" required autocomplete="new-password" />
                                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-frappe-red" />
                                    </div>

                                    <div>
                                        <x-input-label for="register-password_confirmation" :value="__('Confirm Password')" class="text-frappe-text" />
                                        <x-text-input id="register-password_confirmation" class="block mt-1 w-full bg-frappe-surface0 border-frappe-surface1 text-frappe-text" type="password" name="password_confirmation" required autocomplete="new-password" />
                                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-frappe-red" />
                                    </div>

                                    <div>
                                        <x-input-label for="register-role" :value="__('Role')" class="text-frappe-text" />
                                        <select id="register-role" name="role" class="block mt-1 w-full rounded-md bg-frappe-surface0 border-frappe-surface1 text-frappe-text focus:border-frappe-blue focus:ring-frappe-blue">
                                            <option value="client">Client</option>
                                            <option value="provider">Service Provider</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('role')" class="mt-2 text-frappe-red" />
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <a class="text-sm text-frappe-blue hover:text-frappe-sapphire" href="{{ route('login') }}">
                                            {{ __('Already registered?') }}
                                        </a>
                                    </div>

                                    <x-primary-button class="w-full justify-center bg-frappe-green hover:bg-frappe-teal">
                                        {{ __('Register') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-frappe-mantle py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-frappe-subtext1">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Booking System') }}. All rights reserved.</p>
            </div>
        </footer>
    </div>

    <!-- Tab Switching Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');

            // Handle tab clicks
            tabButtons.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active styles from all tabs
                    tabButtons.forEach(t => {
                        t.classList.remove('text-frappe-blue', 'border-frappe-blue');
                        t.classList.add('text-frappe-subtext1');
                        t.classList.remove('border-b-4');
                    });

                    // Add active styles to clicked tab
                    this.classList.add('text-frappe-blue', 'border-b-4', 'border-frappe-blue');
                    this.classList.remove('text-frappe-subtext1');

                    // Toggle forms
                    if (this.id === 'login-tab') {
                        loginForm.classList.remove('hidden');
                        registerForm.classList.add('hidden');
                    } else {
                        registerForm.classList.remove('hidden');
                        loginForm.classList.add('hidden');
                    }
                });
            });

            // Handle anchor links
            if (window.location.hash === '#register') {
                document.getElementById('register-tab').click();
            }
        });
    </script>
</body>
</html>