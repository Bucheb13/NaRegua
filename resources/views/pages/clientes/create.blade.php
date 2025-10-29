@extends('layouts.app')

@section('title', 'Novo Cliente')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold text-[#5A3825] mb-6">Cadastrar Novo Cliente</h1>

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('clientes.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-[#5A3825] font-semibold">Nome</label>
            <input type="text" name="nome" value="{{ old('nome') }}" class="w-full border rounded p-2" required>
        </div>

        <div>
            <label class="block text-[#5A3825] font-semibold">Telefone</label>
            <input type="text" name="telefone" value="{{ old('telefone') }}" class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block text-[#5A3825] font-semibold">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block text-[#5A3825] font-semibold">Data de Nascimento</label>
            <input type="date" name="data_nascimento" value="{{ old('data_nascimento') }}" class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block text-[#5A3825] font-semibold">Barbearia</label>
            <select name="barbearia_id" class="w-full border rounded p-2">
                <option value="">Selecione...</option>
                @foreach($barbearias as $barbearia)
                    <option value="{{ $barbearia->id }}" {{ old('barbearia_id') == $barbearia->id ? 'selected' : '' }}>
                        {{ $barbearia->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-[#5A3825] font-semibold">Observações</label>
            <textarea name="observacoes" class="w-full border rounded p-2" rows="3">{{ old('observacoes') }}</textarea>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('clientes.index') }}" class="px-4 py-2 bg-gray-300 text-[#5A3825] rounded">Cancelar</a>
            <button type="submit" class="px-4 py-2 bg-[#CFAE70] text-[#5A3825] font-semibold rounded">Salvar</button>
        </div>
    </form>
</div>
@endsection
