<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

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
            background: linear-gradient(135deg, rgba(166, 209, 137, 0.25), rgba(166, 209, 137, 0.35));
            backdrop-filter: blur(8px);
            border: 1px solid rgba(166, 209, 137, 0.35);
            color: white;
            transition: all 0.3s ease;
        }

        .frosted-button-register:hover {
            background: linear-gradient(135deg, rgba(166, 209, 137, 0.4), rgba(166, 209, 137, 0.5));
        }

        /* Add missing styles from app.blade.php */
        .nav-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 1000;
        }

        .nav-frosted {
            background: rgba(49, 50, 68, 0.8);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(186, 194, 222, 0.15);
        }

        .dropdown-menu {
            z-index: 10000 !important;
        }

        .main-content {
            margin-top: 64px;
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
        });
    </script>
</body>

</html>
