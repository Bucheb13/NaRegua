<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - NaRegua</title>
    
    <!-- Vite (Laravel 10+) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Favicon opcional -->
    <link rel="icon" href="{{ asset('favicon.ico') }}">
</head>
<body class="bg-[#5A3825] min-h-screen flex items-center justify-center">

    <main class="w-full px-4">
        @yield('content')
    </main>

    <!-- Mensagens de sessão (opcional) -->
    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-100 text-green-700 px-4 py-2 rounded shadow">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 bg-red-100 text-red-700 px-4 py-2 rounded shadow">
            {{ session('error') }}
        </div>
    @endif
</body>
</html>
