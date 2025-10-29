@extends('layouts.app')

@section('title', 'Editar Produto')

@section('content')
<style>
/* Remove spinners (setas) dos inputs number */
.no-spinner::-webkit-inner-spin-button,
.no-spinner::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.no-spinner {
    -moz-appearance: textfield;
}
</style>

<div class="p-8 min-h-screen bg-gradient-to-b from-[#1a1410] to-[#2a1f1a] text-[#f5e6d3] flex justify-center">
    <div class="w-full max-w-2xl">

        {{-- Cabeçalho --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-extrabold bg-gradient-to-r from-yellow-500 to-yellow-300 bg-clip-text text-transparent">
                Editar Produto
            </h1>

            <a href="{{ route('produtos.index') }}"
               class="px-5 py-2 rounded-lg bg-red-600/60 hover:bg-red-600/80
                      transition font-semibold text-white">
                Voltar
            </a>
        </div>

        {{-- Form Glass --}}
        <form action="{{ route('produtos.update', $produto) }}" method="POST"
              class="p-6 bg-white/5 backdrop-blur-xl border border-yellow-500/20 
                     rounded-2xl shadow-[0_0_18px_rgba(255,199,44,0.08)] space-y-6">
            @csrf
            @method('PUT')

            {{-- Nome --}}
            <div>
                <label class="block text-sm text-yellow-300/80 mb-1">Nome do Produto</label>
                <input type="text" name="nome" value="{{ old('nome', $produto->nome) }}"
                       class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2
                              text-[#f5e6d3] focus:ring-1 focus:ring-yellow-500/30"
                       required>
            </div>

            {{-- Preço --}}
            <div>
                <label class="block text-sm text-yellow-300/80 mb-1">Preço (R$)</label>
                <input type="number" step="0.01" name="preco" value="{{ old('preco', $produto->preco) }}"
                       class="no-spinner w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2
                              text-[#f5e6d3] focus:ring-1 focus:ring-yellow-500/30"
                       required>
            </div>

            {{-- Quantidade --}}
            <div>
                <label class="block text-sm text-yellow-300/80 mb-1">Quantidade em Estoque</label>
                <input type="number" name="quantidade_estoque" value="{{ old('quantidade_estoque', $produto->quantidade_estoque) }}"
                       class="no-spinner w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2
                              text-[#f5e6d3] focus:ring-1 focus:ring-yellow-500/30"
                       required>
            </div>

            {{-- Descrição --}}
            <div>
                <label class="block text-sm text-yellow-300/80 mb-1">Descrição</label>
                <textarea name="descricao" rows="3"
                          class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2
                                 text-[#f5e6d3] focus:ring-1 focus:ring-yellow-500/30">{{ old('descricao', $produto->descricao) }}</textarea>
            </div>

            {{-- Barbearia (apenas admin) --}}
            @if($usuario->tipo === 'admin')
                <div>
                    <label class="block text-sm text-yellow-300/80 mb-1">Barbearia</label>
                    <select name="barbearia_id"
                            class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2
                                   text-[#f5e6d3] focus:ring-1 focus:ring-yellow-500/30" required>
                        @foreach($barbearias as $barbearia)
                            <option value="{{ $barbearia->id }}" {{ old('barbearia_id', $produto->barbearia_id) == $barbearia->id ? 'selected' : '' }}>
                                {{ $barbearia->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Ações --}}
            <div class="flex justify-end space-x-3 pt-2">
                <a href="{{ route('produtos.index') }}"
                   class="px-5 py-2 rounded-lg bg-red-600/60 hover:bg-red-600/80 text-white transition font-semibold">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-gradient-to-r from-yellow-600 to-yellow-400
                               text-[#1a1410] font-semibold shadow-lg hover:shadow-xl transition">
                    Atualizar
                </button>
            </div>

        </form>

    </div>
</div>
@endsection
