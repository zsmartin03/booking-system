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

        .frosted-card {
            background: rgba(30, 30, 46, 0.7);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(186, 194, 222, 0.1);
            box-shadow: 0 12px 40px rgba(30, 30, 46, 0.3);
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

        .frosted-button-save {
            background: linear-gradient(135deg, rgba(166, 209, 137, 0.25), rgba(166, 209, 137, 0.35));
            backdrop-filter: blur(8px);
            border: 1px solid rgba(166, 209, 137, 0.35);
            color: white;
            transition: all 0.3s ease;
        }

        .frosted-button-save:hover {
            background: linear-gradient(135deg, rgba(166, 209, 137, 0.4), rgba(166, 209, 137, 0.5));
        }

        .content-layer {
            position: relative;
            z-index: 10;
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

        /* Add missing styles from app.blade.php */
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

        /* Force navbar to stay fixed - override any conflicting styles */
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

        .main-content {
            margin-top: 64px !important;
            padding-top: 0 !important;
        }

        /* Standardized input styling */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="time"],
        input[type="tel"],
        input[type="file"],
        input[type="url"],
        textarea,
        select {
            background: rgba(30, 30, 46, 0.8) !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(186, 194, 222, 0.2) !important;
            color: #cdd6f4 !important;
            border-radius: 0.5rem !important;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="number"]:focus,
        input[type="time"]:focus,
        input[type="tel"]:focus,
        input[type="file"]:focus,
        textarea:focus,
        select:focus {
            outline: none !important;
            border-color: rgba(137, 180, 250, 0.5) !important;
            box-shadow: 0 0 0 2px rgba(137, 180, 250, 0.2) !important;
        }

        textarea {
            resize: vertical !important;
        }

        /* Select dropdown styling */
        select {
            background: rgba(30, 30, 46, 0.8) !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(186, 194, 222, 0.2) !important;
            color: #cdd6f4 !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem !important;
            appearance: none !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23cdd6f4' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e") !important;
            background-position: right 0.5rem center !important;
            background-repeat: no-repeat !important;
            background-size: 1.5em 1.5em !important;
            padding-right: 2.5rem !important;
        }

        select:focus {
            outline: none !important;
            border-color: rgba(137, 180, 250, 0.5) !important;
            box-shadow: 0 0 0 2px rgba(137, 180, 250, 0.2) !important;
        }

        select:hover {
            background: rgba(30, 30, 46, 0.9) !important;
            border-color: rgba(186, 194, 222, 0.3) !important;
        }

        /* Option styling */
        select option {
            background: rgba(30, 30, 46, 0.95) !important;
            color: #cdd6f4 !important;
            padding: 0.5rem !important;
        }

        select option:hover,
        select option:focus,
        select option:checked {
            background: rgba(137, 180, 250, 0.3) !important;
            color: #cdd6f4 !important;
        }

        select option:selected {
            background: rgba(137, 180, 250, 0.4) !important;
            color: #ffffff !important;
        }

        /* WebKit specific option styling */
        select option:checked {
            background: rgba(137, 180, 250, 0.4) linear-gradient(0deg, rgba(137, 180, 250, 0.4) 0%, rgba(137, 180, 250, 0.4) 100%) !important;
        }

        /* Custom dropdown arrow styling */
        .custom-select {
            position: relative;
        }

        .custom-select::after {
            content: '▼';
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #cdd6f4;
            pointer-events: none;
            font-size: 0.8rem;
        }

        .frosted-glass {
            background: rgba(49, 50, 68, 0.35);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(186, 194, 222, 0.15);
            box-shadow: 0 8px 32px rgba(30, 30, 46, 0.2);
        }
    </style>
</head>

<body class="min-h-screen bg-frappe-crust font-sans text-frappe-text antialiased wave-container">
    <div class="min-h-screen flex flex-col content-layer">
        @include('layouts.navigation')
        <div class="main-content flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="w-full sm:max-w-md px-6 py-8">
                {{ $slot }}
            </div>
        </div>
    </div>

    <script>
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
