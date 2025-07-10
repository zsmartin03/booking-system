<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#8839ef">

    <title>{{ config('app.name', 'BookingSystem') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Map Libraries -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Geoapify API Key -->
    <meta name="geoapify-api-key" content="{{ config('services.geoapify.key') }}">

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
            /* px-4 py-2 */
            border-radius: 0.5rem !important;
            /* rounded-lg */
            font-size: 0.875rem !important;
            /* text-sm */
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

        .frosted-modal {
            background: rgba(30, 30, 46, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(186, 194, 222, 0.15);
        }

        .content-layer {
            position: relative;
            z-index: 10;
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

        /* Language switcher needs higher z-index */
        .language-switcher {
            position: relative;
            z-index: 1000;
        }

        /* Ensure dropdowns stay above everything */
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

        /* Header frosted glass */
        .header-frosted {
            background: rgba(49, 50, 68, 0.5);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(186, 194, 222, 0.1);
        }

        .main-content {
            margin-top: 64px !important;
            /* Height of navbar */
            padding-top: 0 !important;
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



        /* Standardized button styles */
        .edit-button {
            background: linear-gradient(to right, rgba(137, 180, 250, 0.20), rgba(99, 102, 241, 0.20)) !important;
            backdrop-filter: blur(6px) !important;
            border: 1px solid rgba(137, 180, 250, 0.30) !important;
            color: #89b4fa !important;
            padding: 0.5rem 1rem !important;
            /* px-4 py-2 */
            border-radius: 0.5rem !important;
            /* rounded-lg */
            font-size: 0.875rem !important;
            /* text-sm */
            transition: all 0.3s ease !important;
        }

        .edit-button:hover {
            background: linear-gradient(to right, rgba(137, 180, 250, 0.30), rgba(99, 102, 241, 0.30)) !important;
        }

        .delete-button {
            background: linear-gradient(to right, rgba(243, 139, 168, 0.20), rgba(245, 85, 85, 0.20)) !important;
            backdrop-filter: blur(6px) !important;
            border: 1px solid rgba(243, 139, 168, 0.30) !important;
            color: #f38ba8 !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem !important;
            font-size: 0.875rem !important;
            transition: all 0.3s ease !important;
        }

        .delete-button:hover {
            background: linear-gradient(to right, rgba(243, 139, 168, 0.30), rgba(245, 85, 85, 0.30)) !important;
        }

        .save-button {
            background: linear-gradient(to right, rgba(166, 209, 137, 0.20), rgba(166, 209, 137, 0.20)) !important;
            backdrop-filter: blur(6px) !important;
            border: 1px solid rgba(166, 209, 137, 0.30) !important;
            color: #a6d189 !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem !important;
            font-size: 0.875rem !important;
            transition: all 0.3s ease !important;
        }

        .save-button:hover {
            background: linear-gradient(to right, rgba(166, 209, 137, 0.30), rgba(166, 209, 137, 0.30)) !important;
        }

        .action-button {
            background: linear-gradient(to right, rgba(137, 180, 250, 0.20), rgba(137, 180, 250, 0.20)) !important;
            backdrop-filter: blur(6px) !important;
            border: 1px solid rgba(137, 180, 250, 0.30) !important;
            color: #89b4fa !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem !important;
            font-size: 0.875rem !important;
            transition: all 0.3s ease !important;
        }

        .action-button:hover {
            background: linear-gradient(to right, rgba(137, 180, 250, 0.30), rgba(137, 180, 250, 0.30)) !important;
        }

        /* Additional gradient button styles */
        a.frosted-button-green,
        .frosted-button-green {
            background: linear-gradient(135deg, rgba(166, 209, 137, 0.25), rgba(166, 209, 137, 0.35)) !important;
            backdrop-filter: blur(8px) !important;
            border: 1px solid rgba(166, 209, 137, 0.35) !important;
            color: white !important;
            transition: all 0.3s ease !important;
        }

        a.frosted-button-green:hover,
        .frosted-button-green:hover {
            background: linear-gradient(135deg, rgba(166, 209, 137, 0.4), rgba(166, 209, 137, 0.5)) !important;
        }

        a.frosted-button-peach,
        .frosted-button-peach {
            background: linear-gradient(135deg, rgba(239, 159, 118, 0.25), rgba(239, 159, 118, 0.35)) !important;
            backdrop-filter: blur(8px) !important;
            border: 1px solid rgba(239, 159, 118, 0.35) !important;
            color: white !important;
            transition: all 0.3s ease !important;
        }

        a.frosted-button-peach:hover,
        .frosted-button-peach:hover {
            background: linear-gradient(135deg, rgba(239, 159, 118, 0.4), rgba(239, 159, 118, 0.5)) !important;
        }

        a.frosted-button-lavender,
        .frosted-button-lavender {
            background: linear-gradient(135deg, rgba(186, 187, 241, 0.25), rgba(186, 187, 241, 0.35)) !important;
            backdrop-filter: blur(8px) !important;
            border: 1px solid rgba(186, 187, 241, 0.35) !important;
            color: white !important;
            transition: all 0.3s ease !important;
        }

        a.frosted-button-lavender:hover,
        .frosted-button-lavender:hover {
            background: linear-gradient(135deg, rgba(186, 187, 241, 0.4), rgba(186, 187, 241, 0.5)) !important;
        }

        a.frosted-button-mauve,
        .frosted-button-mauve {
            background: linear-gradient(135deg, rgba(202, 158, 230, 0.25), rgba(202, 158, 230, 0.35)) !important;
            backdrop-filter: blur(8px) !important;
            border: 1px solid rgba(202, 158, 230, 0.35) !important;
            color: white !important;
            transition: all 0.3s ease !important;
        }

        a.frosted-button-mauve:hover,
        .frosted-button-mauve:hover {
            background: linear-gradient(135deg, rgba(202, 158, 230, 0.4), rgba(202, 158, 230, 0.5)) !important;
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

        /* Employee card styles */
        .employee-card {
            background: linear-gradient(to right, rgba(166, 209, 137, 0.12), rgba(137, 180, 250, 0.10)) !important;
            backdrop-filter: blur(6px) !important;
            border: 1px solid rgba(186, 194, 222, 0.18) !important;
            color: #a6d189 !important;
            transition: all 0.3s ease !important;
            cursor: pointer;
            border-radius: 0.5rem !important;
            box-shadow: 0 2px 8px 0 rgba(30, 30, 46, 0.06);
        }

        .employee-card.selected {
            background: linear-gradient(to right, rgba(137, 180, 250, 0.22), rgba(137, 180, 250, 0.32)) !important;
            border: 1.5px solid rgba(137, 180, 250, 0.40) !important;
            color: #89b4fa !important;
            box-shadow: 0 4px 16px 0 rgba(137, 180, 250, 0.10);
        }

        .employee-card.unavailable {
            opacity: 0.6;
            pointer-events: none;
        }

        .employee-card:hover:not(.selected):not(.unavailable) {
            background: linear-gradient(to right, rgba(166, 209, 137, 0.18), rgba(137, 180, 250, 0.18)) !important;
            border-color: rgba(166, 209, 137, 0.28) !important;
        }

        .frosted-button-cancel {
            background: linear-gradient(to right, rgba(237, 135, 150, 0.20), rgba(210, 120, 137, 0.20)) !important;
            backdrop-filter: blur(6px) !important;
            border: 1px solid rgba(237, 135, 150, 0.30) !important;
            color: #ed8796 !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem !important;
            font-size: 0.875rem !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
            cursor: pointer !important;
        }

        .frosted-button-cancel:hover {
            background: linear-gradient(to right, rgba(237, 135, 150, 0.30), rgba(210, 120, 137, 0.30)) !important;
        }
    </style>
</head>

<body class="font-sans antialiased bg-frappe-base text-frappe-text wave-container">
    <div class="min-h-screen flex flex-col content-layer">
        @include('layouts.navigation')

        <div class="main-content ">
            <!-- Page Heading -->
            @if (isset($header))
                <header class="header-frosted shadow">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const savedLanguage = localStorage.getItem('language');
            const currentLanguage = '{{ app()->getLocale() }}';

            if (savedLanguage && savedLanguage !== currentLanguage) {
                window.location.href = '/locale/init/' + savedLanguage;
            }
        });
    </script>
</body>

</html>
