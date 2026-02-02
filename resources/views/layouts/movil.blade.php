<!DOCTYPE html>
<html lang="es" class="h-100" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>Intranet | @yield('title')</title>
    @include('includes.head')
</head>

<body class="bg-cream">
    <div class="container-fluid">
        @include('includes.header')
        <div class="m-0 p-0 mt-5 mb-5">
            @yield('content')
        </div>
    </div>
</body>
<!---
<footer>
    @include('includes.footer')
</footer>
---->

</html>
