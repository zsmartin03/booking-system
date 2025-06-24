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
        .wave-container {
            position: relative;
            min-height: 100vh;
            background: linear-gradient(135deg, #8839ef, #7c3aed, #6366f1);
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
            z-index: -2;
            pointer-events: none;
        }

        body {
            background: linear-gradient(135deg, #8839ef, #7c3aed, #6366f1);
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: 100% 100%;
            min-height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
        }

        html {
            overscroll-behavior: none;
            background: linear-gradient(135deg, #8839ef, #7c3aed, #6366f1);
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: 100% 100%;
        }

        .content-layer {
            position: relative;
            z-index: 10;
        }

        .frosted-card {
            background: rgba(30, 30, 46, 0.7);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(186, 194, 222, 0.1);
            box-shadow: 0 12px 40px rgba(30, 30, 46, 0.3);
        }
    </style>
</head>

<body class="min-h-screen bg-frappe-crust font-sans text-frappe-text antialiased wave-container">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 content-layer">
        <!-- Language Switcher for Guest Layout -->
        <div class="absolute top-4 right-4">
            <x-language-switcher />
        </div>

        <div class="w-full sm:max-w-md px-6 py-8">
            {{ $slot }}
        </div>
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
