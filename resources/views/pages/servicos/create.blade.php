@extends('layouts.app')

@section('title', 'Novo Serviço')

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

<div class="p-8 min-h-screen bg-[#1a1410]/10 backdrop-blur-sm text-[#f5e6d3] space-y-10 flex justify-center">
    <div class="w-full max-w-2xl">

        {{-- TÍTULO --}}
        <h1 class="text-4xl font-orbitron tracking-wide inline-block
           bg-gradient-to-r from-yellow-600 via-yellow-500 to-yellow-400
           bg-clip-text text-transparent mb-6">
           Cadastrar Serviço
</h1>


        {{-- FORM GLASS --}}
        <form action="{{ route('servicos.store') }}" method="POST"
              class="p-6 bg-white/5 backdrop-blur-xl border border-yellow-500/20 
                     rounded-2xl shadow-[0_0_18px_rgba(255,199,44,0.08)] space-y-6">
            @csrf

            {{-- BARBEARIA --}}
            <div>
                <label class="block text-sm text-yellow-300/80 mb-1">Barbearia</label>
                <select name="barbearia_id"
                        class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2 text-[#f5e6d3]
                               focus:ring-1 focus:ring-yellow-500/30"
                        @if($usuario->tipo !== 'admin') disabled @endif>
                    @foreach($barbearias as $b)
                        <option value="{{ $b->id }}">{{ $b->nome }}</option>
                    @endforeach
                </select>
            </div>

            {{-- NOME --}}
            <div>
                <label class="block text-sm text-yellow-300/80 mb-1">Nome do Serviço</label>
                <input type="text" name="nome" value="{{ old('nome') }}"
                       class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2 text-[#f5e6d3]
                              focus:ring-1 focus:ring-yellow-500/30"
                       required>
            </div>

            {{-- DESCRIÇÃO --}}
            <div>
                <label class="block text-sm text-yellow-300/80 mb-1">Descrição</label>
                <textarea name="descricao" rows="3"
                          class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2
                                 text-[#f5e6d3] focus:ring-1 focus:ring-yellow-500/30">{{ old('descricao') }}</textarea>
            </div>

            {{-- PREÇO --}}
            <div>
                <label class="block text-sm text-yellow-300/80 mb-1">Preço (R$)</label>
                <input type="number" step="0.01" name="preco" value="{{ old('preco') }}"
                       class="no-spinner w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2 text-[#f5e6d3]
                              focus:ring-1 focus:ring-yellow-500/30"
                       required>
            </div>

            {{-- DURAÇÃO --}}
            <div>
                <label class="block text-sm text-yellow-300/80 mb-1">Duração (minutos)</label>
                <input type="number" name="duracao_minutos" value="{{ old('duracao_minutos') }}"
                       class="no-spinner w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2 text-[#f5e6d3]
                              focus:ring-1 focus:ring-yellow-500/30"
                       required>
            </div>

            {{-- AÇÕES --}}
            <div class="flex justify-end space-x-3 pt-2">
                <a href="{{ route('servicos.index') }}"
                   class="px-5 py-2 rounded-lg bg-red-600/60 hover:bg-red-600/80 text-white transition font-semibold">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-gradient-to-r from-yellow-600 to-yellow-400
                               text-[#1a1410] font-semibold shadow-lg hover:shadow-xl transition">
                    Salvar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
