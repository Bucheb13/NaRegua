@extends('layouts.app')

@section('title', 'Serviços')

@section('content')
<div class="p-8 min-h-screen bg-gradient-to-b from-[#1a1410] to-[#2a1f1a] text-[#f5e6d3] space-y-8">

    {{-- Cabeçalho --}}
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-extrabold bg-gradient-to-r from-yellow-500 to-yellow-300 bg-clip-text text-transparent">
            Serviços
        </h1>

        @if(in_array($usuario->tipo, ['admin', 'barbeiro']))
            <a href="{{ route('servicos.create') }}"
               class="px-5 py-2 rounded-xl bg-gradient-to-r from-yellow-600 to-yellow-400
                      text-[#1a1410] font-semibold shadow-lg hover:shadow-xl transition">
               + Novo Serviço
            </a>
        @endif
    </div>

    {{-- Dropdown — apenas admin --}}
    @if($usuario->tipo === 'admin')
        <div class="bg-[#1a1410]/50 border border-yellow-500/20 rounded-xl p-4 backdrop-blur-md w-fit">
            <label for="barbearia" class="text-sm text-yellow-300/80">Filtrar por Barbearia:</label>
            <select id="barbearia"
                    class="bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2
                           text-[#f5e6d3] focus:ring-1 focus:ring-yellow-500/30"
                    onchange="window.location='{{ route('servicos.index') }}?barbearia_id=' + this.value">
                @foreach($barbearias as $b)
                    <option value="{{ $b->id }}" @if($barbeariaSelecionada->id == $b->id) selected @endif>
                        {{ $b->nome }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif

    {{-- Mensagem de sucesso --}}
    @if(session('success'))
        <div class="bg-green-600/20 text-green-300 px-4 py-3 rounded-xl border border-green-600/30">
            {{ session('success') }}
        </div>
    @endif

    {{-- LISTAGEM GLASS --}}
    <div class="space-y-4 mt-4">
        @forelse($servicos as $s)
            <div class="relative p-6 rounded-2xl bg-white/5 backdrop-blur-xl border border-yellow-500/20
                        shadow-[0_0_14px_rgba(255,199,44,0.08)] hover:shadow-[0_0_35px_rgba(255,199,44,0.35)]
                        transition-all duration-500">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                    {{-- TEXTO PRINCIPAL --}}
                    <div>
                        <div class="text-xl font-semibold text-yellow-300">
                            {{ $s->nome }}
                        </div>
                        @if($s->descricao)
                            <div class="text-sm text-yellow-200/70 mt-1">{{ $s->descricao }}</div>
                        @endif
                        <div class="text-xs mt-2 text-yellow-300/60">
                            Duração: {{ $s->duracao_minutos }} min • Barbearia: {{ $s->barbearia->nome ?? '-' }}
                        </div>
                    </div>

                    {{-- PREÇO + AÇÕES --}}
                    <div class="text-right">
                        <div class="text-lg font-bold text-yellow-400">
                            R$ {{ number_format($s->preco, 2, ',', '.') }}
                        </div>

                        <div class="mt-3 flex gap-2 justify-end">
                            @if(in_array($usuario->tipo, ['admin', 'barbeiro']))
                                <a href="{{ route('servicos.edit', $s) }}"
                                   class="px-3 py-1.5 text-xs rounded-lg bg-yellow-500/25 hover:bg-yellow-500/40
                                          transition font-semibold text-yellow-200">
                                    Editar
                                </a>
                            @endif

                            @if($usuario->tipo === 'admin')
                                <form action="{{ route('servicos.destroy', $s) }}" method="POST"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este serviço?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-1.5 text-xs rounded-lg bg-red-600/60 hover:bg-red-600/80
                                               transition font-semibold text-white">
                                        Excluir
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-yellow-300/70 py-6">Nenhum serviço cadastrado.</div>
        @endforelse
    </div>

</div>
@endsection
