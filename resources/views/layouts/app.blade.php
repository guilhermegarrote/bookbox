<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', ''){{ !empty(trim($__env->yieldContent('title'))) ? ' | Bookbox' : 'Bookbox' }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        window.App = {
            filterData: @json($filterData ?? []),
            csrfToken: '{{ csrf_token() }}',
            filterUrl: @json($filterUrl ?? null)
        };
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @routes
</head>

<body>
    <x-layout.top-nav :filter-url="$filterUrl ?? null" />

    <main>
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>
