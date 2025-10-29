@extends('layouts.app')

@section('title', 'Novo Agendamento')

@section('content')
<div class="p-8 min-h-screen bg-gradient-to-b from-[#1a1410] to-[#2a1f1a] text-[#f5e6d3] flex justify-center">
    <div class="w-full max-w-lg">

        {{-- TÍTULO --}}
        <h1 class="text-3xl font-extrabold bg-gradient-to-r from-yellow-500 to-yellow-300 bg-clip-text text-transparent mb-6">
            Novo Agendamento
        </h1>

        {{-- CARD GLASS --}}
        <form action="{{ route('agendamentos.store') }}" method="POST"
            class="p-6 bg-white/5 backdrop-blur-xl border border-yellow-500/20 rounded-2xl
                   shadow-[0_0_18px_rgba(255,199,44,0.08)] space-y-6">
            @csrf

            <!-- HIDDENS -->
            <input type="hidden" name="barbearia_id" value="{{ $barbearia_id ?? request()->query('barbearia_id') }}">
            <input type="hidden" name="barbeiro_id" value="{{ $barbeiro_id ?? request()->query('barbeiro_id') }}">
            <input type="hidden" name="data_hora" value="{{ $data_hora ?? request()->query('data_hora') }}">
            <input type="hidden" name="status" value="agendado">

            {{-- EXIBE HORA ESCOLHIDA --}}
            <div class="bg-yellow-500/20 border border-yellow-500/30 rounded-xl p-3 text-center text-yellow-300 text-sm font-semibold">
                Horário Selecionado: {{ \Carbon\Carbon::parse($data_hora ?? request()->query('data_hora'))->format('d/m/Y H:i') }}
            </div>

            {{-- CLIENTE --}}
            <div>
                <label class="block text-sm text-yellow-300/80 mb-1">Cliente</label>
                <select name="cliente_id"
                    class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2 text-[#f5e6d3]
                           focus:ring-1 focus:ring-yellow-500/30"
                    required>
                    @foreach($clientes as $c)
                        <option value="{{ $c->id }}">{{ $c->nome }}</option>
                    @endforeach
                </select>
            </div>

            {{-- SERVIÇO --}}
            <div>
                <label class="block text-sm text-yellow-300/80 mb-1">Serviço</label>
                <select name="servico_id"
                    class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2 text-[#f5e6d3]
                           focus:ring-1 focus:ring-yellow-500/30"
                    required>
                    @foreach($servicos as $s)
                        <option value="{{ $s->id }}">{{ $s->nome }} — R$ {{ number_format($s->preco, 2, ',', '.') }}</option>
                    @endforeach
                </select>
            </div>

            {{-- BOTÕES --}}
            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('agendamentos.index') }}"
                    class="px-5 py-2 rounded-lg bg-red-600/60 hover:bg-red-600/80 text-white transition font-semibold">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-5 py-2 rounded-lg bg-gradient-to-r from-yellow-600 to-yellow-400
                           text-[#1a1410] font-semibold shadow-lg hover:shadow-xl transition">
                    Confirmar
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
