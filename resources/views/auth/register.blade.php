@extends('layouts.auth')

@section('title', 'Registrar')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded-xl shadow-lg mt-20">
    <h2 class="text-3xl font-bold text-[#5A3825] mb-6 text-center">NaRegua - Cadastro</h2>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Nome</label>
            <input type="text" name="nome" value="{{ old('nome') }}" required
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#CFAE70]">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#CFAE70]">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Senha</label>
            <input type="password" name="password" required
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#CFAE70]">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Confirmar Senha</label>
            <input type="password" name="password_confirmation" required
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#CFAE70]">
        </div>

        <button type="submit" class="w-full bg-[#CFAE70] text-[#5A3825] font-bold py-2 rounded-lg hover:bg-[#b8985a]">
            Registrar
        </button>
    </form>

    <div class="mt-4 text-center">
        <a href="{{ route('login') }}" class="text-[#CFAE70] hover:underline">Já tem uma conta? Entrar</a>
    </div>
</div>
@endsection
