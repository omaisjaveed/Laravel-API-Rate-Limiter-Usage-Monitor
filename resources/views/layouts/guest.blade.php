<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#ecfdf8',
                            100: '#d1faf0',
                            500: '#0d9488',
                            600: '#0f766e',
                            700: '#115e59',
                            900: '#134e4a',
                        }
                    },
                    fontFamily: {
                        sans: ['Segoe UI', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased">
    <div class="min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            @yield('content')
        </div>
    </div>
</body>
</html>
