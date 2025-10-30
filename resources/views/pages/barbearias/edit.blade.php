@extends('layouts.app')

@section('title', 'Editar Barbearia')

@section('content')
<div class="p-8 min-h-screen bg-[#1a1410]/10 backdrop-blur-sm text-[#f5e6d3] space-y-10">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-yellow-600 via-yellow-500 to-yellow-300 bg-clip-text text-transparent">
            Editar Barbearia
        </h1>

        <a href="{{ route('dashboard.index') }}"
           class="px-4 py-2 rounded-lg bg-[#2a1f1a]/70 border border-yellow-500/20 text-yellow-200 hover:bg-[#3a2a1f]/70 transition">
           Voltar
        </a>
    </div>

    <div class="bg-[#1a1410]/60 backdrop-blur-xl border border-yellow-500/20 rounded-2xl p-6 shadow-xl">
        <form action="{{ route('dashboard.update', $barbearia) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Nome --}}
                <div>
                    <label class="block text-sm font-semibold text-yellow-300 mb-2">Nome</label>
                    <input type="text" name="nome"
                           class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2.5 text-[#f5e6d3] focus:ring-1 focus:ring-yellow-500/30"
                           value="{{ old('nome', $barbearia->nome) }}" required>
                </div>

                {{-- Responsável --}}
                <div>
                    <label class="block text-sm font-semibold text-yellow-300 mb-2">Responsável</label>
                    <input type="text" name="responsavel_nome"
                           class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2.5 text-[#f5e6d3] focus:ring-1 focus:ring-yellow-500/30"
                           value="{{ old('responsavel_nome', $barbearia->responsavel_nome) }}">
                </div>

                {{-- CNPJ --}}
                <div>
                    <label class="block text-sm font-semibold text-yellow-300 mb-2">CNPJ</label>
                    <input type="text" name="cnpj"
                           class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2.5 text-[#f5e6d3] focus:ring-1 focus:ring-yellow-500/30"
                           value="{{ old('cnpj', $barbearia->cnpj) }}">
                </div>

                {{-- Telefone --}}
                <div>
                    <label class="block text-sm font-semibold text-yellow-300 mb-2">Telefone</label>
                    <input type="text" name="telefone"
                           class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2.5 text-[#f5e6d3] focus:ring-1 focus:ring-yellow-500/30"
                           value="{{ old('telefone', $barbearia->telefone) }}">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-semibold text-yellow-300 mb-2">E-mail</label>
                    <input type="email" name="email"
                           class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2.5 text-[#f5e6d3] focus:ring-1 focus:ring-yellow-500/30"
                           value="{{ old('email', $barbearia->email) }}">
                </div>

                {{-- Endereço --}}
                <div>
                    <label class="block text-sm font-semibold text-yellow-300 mb-2">Endereço</label>
                    <input type="text" name="endereco"
                           class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2.5 text-[#f5e6d3] focus:ring-1 focus:ring-yellow-500/30"
                           value="{{ old('endereco', $barbearia->endereco) }}">
                </div>

                {{-- Validade --}}
                <div>
                    <label class="block text-sm font-semibold text-yellow-300 mb-2">Validade da Licença</label>
                    <input type="date" name="licenca_validade"
                           class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2.5 text-[#f5e6d3] focus:ring-1 focus:ring-yellow-500/30"
                           value="{{ old('licenca_validade', optional($barbearia->licenca_validade)->format('Y-m-d')) }}">
                </div>

                {{-- Logo Upload --}}
                <div>
                    <label class="block text-sm font-semibold text-yellow-300 mb-2">Logo</label>
                    <input type="file" name="logo"
                           class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2.5 text-[#f5e6d3]"
                           accept="image/*">

                    @if($barbearia->logo)
                        <div class="mt-3">
                            <img src="{{ Storage::url($barbearia->logo) }}"
                                 class="w-24 h-24 object-cover rounded-xl border border-yellow-500/30 shadow">
                        </div>
                    @endif
                </div>

            </div>

            <button type="submit"
                    class="mt-8 px-6 py-2.5 rounded-xl bg-gradient-to-r from-yellow-600 via-yellow-500 to-yellow-400 
                           text-[#1a1410] font-extrabold shadow-lg hover:shadow-xl transition">
                Salvar Alterações
            </button>
        </form>
    </div>
</div>
@endsection
