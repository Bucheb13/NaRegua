@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
<div class="p-8 min-h-screen bg-gradient-to-b from-[#1a1410] to-[#2a1f1a] text-[#f5e6d3] space-y-8">

    {{-- Cabeçalho --}}
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-extrabold bg-gradient-to-r from-yellow-500 to-yellow-300 bg-clip-text text-transparent">
            Produtos
        </h1>

        @if($usuario->tipo !== 'cliente')
            <a href="{{ route('produtos.create') }}"
               class="px-5 py-2 rounded-xl bg-gradient-to-r from-yellow-600 to-yellow-400
                      text-[#1a1410] font-semibold shadow-lg hover:shadow-xl transition">
               + Novo Produto
            </a>
        @endif
    </div>

   {{-- Filtro admin --}}
   @if($usuario->tipo === 'admin')
   <div class="bg-[#1a1410]/40 border border-yellow-500/15 rounded-md p-3 w-full md:w-1/3">
       <label class="text-sm text-yellow-200/80 mb-1 block">Filtrar por Barbearia:</label>
       <select class="w-full bg-[#241b16]/50 border border-yellow-500/20 rounded-sm px-3 py-2 text-[#f5e6d3]
                      focus:ring-1 focus:ring-yellow-500/30 focus:outline-none"
               onchange="window.location='{{ route('produtos.index') }}?barbearia_id=' + this.value">
           <option value="">Todos</option>
           @foreach($barbearias as $b)
               <option value="{{ $b->id }}" {{ ($barbeariaSelecionada && $barbeariaSelecionada->id == $b->id) ? 'selected' : '' }}>
                   {{ $b->nome }}
               </option>
           @endforeach
       </select>
   </div>
   @endif
   


    {{-- Mensagem de Sucesso --}}
    @if(session('success'))
        <div class="bg-green-600/20 text-green-300 px-4 py-3 rounded-xl border border-green-600/30">
            {{ session('success') }}
        </div>
    @endif

    {{-- LISTAGEM: Glass Premium --}}
    <div class="space-y-4 mt-4">
        @forelse($produtos as $produto)
            <div class="relative p-6 rounded-2xl bg-white/5 backdrop-blur-xl border border-yellow-500/20
                        shadow-[0_0_14px_rgba(255,199,44,0.08)] hover:shadow-[0_0_35px_rgba(255,199,44,0.35)]
                        transition-all duration-500">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                    {{-- Nome + Estoque --}}
                    <div>
                        <div class="text-xl font-semibold text-yellow-300">
                            {{ $produto->nome }}
                        </div>
                        <div class="text-sm text-yellow-200/70 mt-1">
                            Estoque: {{ $produto->quantidade_estoque }}
                            @if($usuario->tipo === 'admin')
                                • Barbearia: {{ $produto->barbearia->nome ?? '-' }}
                            @endif
                        </div>
                    </div>

                    {{-- Preço + Ações --}}
                    <div class="text-right">
                        <div class="text-lg font-bold text-yellow-400">
                            R$ {{ number_format($produto->preco, 2, ',', '.') }}
                        </div>

                        <div class="mt-3 flex gap-2 justify-end">
                            @if($usuario->tipo === 'admin' || ($usuario->tipo === 'barbeiro' && $produto->barbearia_id === $usuario->barbearia_id))
                                <a href="{{ route('produtos.edit', $produto->id) }}"
                                   class="px-3 py-1.5 text-xs rounded-lg bg-yellow-500/25 hover:bg-yellow-500/40
                                          transition font-semibold text-yellow-200">
                                    Editar
                                </a>
                            @endif

                            @if($usuario->tipo === 'admin' || ($usuario->tipo === 'barbeiro' && $produto->barbearia_id === $usuario->barbearia_id))
                                <form method="POST" action="{{ route('produtos.destroy', $produto->id) }}"
                                      onsubmit="return confirm('Deseja realmente excluir este produto?')" class="inline">
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
            <div class="text-center text-yellow-300/70 py-6">
                Nenhum produto encontrado.
            </div>
        @endforelse
    </div>
</div>
@endsection
