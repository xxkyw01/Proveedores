<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <title>Intranet | @yield('title')</title>
    @include('includes.head')
    @stack('styles')
</head>
<body class="bg-cream d-flex flex-column">
    <header>
        @include('includes.header')
    </header>
    {{-- Contenedor principal --}}
    <div class="wrapper mt-5 mb-5">
        @yield('content')
    </div>

    {{-- <footer>@include('includes.footer')</footer> --}}
    @stack('scripts')
</body>
</html>
