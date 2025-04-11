<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Trung tâm Tiếng Trung') }} - @yield('title', 'Trang chủ')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Styles & Scripts -->
    @if(app()->environment('local'))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('build/assets/app-4c1b63c7.css') }}">
        <script src="{{ asset('build/assets/app-57679e03.js') }}" defer></script>
    @endif
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <div id="app" class="min-h-screen flex flex-col">
        @include('layouts.partials.header')

        <main class="flex-grow">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 mx-4 mt-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 mx-4 mt-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 mx-4 mt-4" role="alert">
                    <p>{{ session('warning') }}</p>
                </div>
            @endif

            @yield('content')
        </main>

        @include('layouts.partials.footer')
    </div>

    <!-- Extra Scripts -->
    @stack('scripts')
</body>
</html> 