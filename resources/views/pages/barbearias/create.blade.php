@extends('layouts.app')

@section('title', 'Nova Barbearia')

@section('content')
<div class="p-8 min-h-screen bg-[#1a1410]/10 backdrop-blur-sm text-[#f5e6d3] space-y-10">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-[#5A3825]">Nova Barbearia</h1>
        <a href="{{ route('barbearias.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded font-semibold shadow">Voltar</a>
    </div>

    <div class="bg-white rounded shadow p-6">
        <form action="{{ route('barbearias.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label class="font-semibold text-gray-700">Nome</label>
                    <input type="text" name="nome" class="w-full border rounded px-3 py-2" value="{{ old('nome') }}" required>
                </div>

                <div>
                    <label class="font-semibold text-gray-700">Responsável</label>
                    <input type="text" name="responsavel_nome" class="w-full border rounded px-3 py-2" value="{{ old('responsavel_nome') }}">
                </div>

                <div>
                    <label class="font-semibold text-gray-700">CNPJ</label>
                    <input type="text" name="cnpj" class="w-full border rounded px-3 py-2" value="{{ old('cnpj') }}">
                </div>

                <div>
                    <label class="font-semibold text-gray-700">Telefone</label>
                    <input type="text" name="telefone" class="w-full border rounded px-3 py-2" value="{{ old('telefone') }}">
                </div>

                <div>
                    <label class="font-semibold text-gray-700">Email</label>
                    <input type="email" name="email" class="w-full border rounded px-3 py-2" value="{{ old('email') }}">
                </div>

                <div>
                    <label class="font-semibold text-gray-700">Endereço</label>
                    <input type="text" name="endereco" class="w-full border rounded px-3 py-2" value="{{ old('endereco') }}">
                </div>

                <div>
                    <label class="font-semibold text-gray-700">Licença Ativa?</label>
                    <select name="licenca_ativa" class="w-full border rounded px-3 py-2">
                        <option value="1" {{ old('licenca_ativa') == 1 ? 'selected' : '' }}>Sim</option>
                        <option value="0" {{ old('licenca_ativa') == 0 ? 'selected' : '' }}>Não</option>
                    </select>
                </div>

                <div>
                    <label class="font-semibold text-gray-700">Validade da Licença</label>
                    <input type="date" name="licenca_validade" class="w-full border rounded px-3 py-2" value="{{ old('licenca_validade') }}">
                </div>

            </div>

            <button type="submit" class="mt-6 bg-[#CFAE70] hover:bg-[#b69355] text-[#5A3825] px-6 py-2 rounded font-bold shadow">Salvar Barbearia</button>
        </form>
    </div>
</div>
@endsection
