<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-transparent">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - NaRegua</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="{{ asset('favicon.ico') }}">
</head>

<body class="h-full min-h-screen flex items-center justify-center relative overflow-hidden bg-transparent">

    <!-- Fundo com imagem -->
    <div class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat"
         style="background-image: url('https://images.unsplash.com/photo-1585747860715-2ba37e788b70?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=874');">
    </div>

    <!-- Overlay escuro (ajustado) -->
    <div class="absolute inset-0 z-10 bg-black/40"></div>

    <main class="relative z-20 w-full px-4">
        @yield('content')
    </main>

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
