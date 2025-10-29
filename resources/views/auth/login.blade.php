@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#1a1410]">
    <div class="w-full max-w-md bg-[#1a1410]/70 backdrop-blur-xl border border-yellow-500/20 rounded-2xl p-8 shadow-2xl">
        <h2 class="text-3xl font-bold text-yellow-300 mb-8 text-center drop-shadow-lg tracking-wide">
            NaRegua
        </h2>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-600/20 text-red-400 border border-red-500/30 rounded">
                @foreach ($errors->all() as $error)
                    <p class="text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-yellow-200/80 text-sm mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2 bg-[#241b16]/60 text-white placeholder-gray-400 border border-yellow-500/20 rounded-lg focus:ring-2 focus:ring-yellow-400/50 focus:outline-none">
            </div>

            <div>
                <label class="block text-yellow-200/80 text-sm mb-2">Senha</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2 bg-[#241b16]/60 text-white placeholder-gray-400 border border-yellow-500/20 rounded-lg focus:ring-2 focus:ring-yellow-400/50 focus:outline-none">
            </div>

            <button type="submit"
                class="w-full py-2 rounded-lg font-semibold text-[#1a1410] bg-gradient-to-r from-yellow-600 via-yellow-500 to-yellow-400 hover:brightness-110 shadow-lg hover:shadow-xl transition duration-200">
                Entrar
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('password.request') }}" class="text-yellow-300/80 hover:text-yellow-400 hover:underline">
                Esqueci minha senha
            </a>
        </div>
    </div>
</div>
@endsection
