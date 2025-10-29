@extends('layouts.app')

@section('title', 'Agendamentos')

@section('content')
<div class="p-8 min-h-screen bg-gradient-to-b from-[#1a1410] to-[#2a1f1a] text-[#f5e6d3] space-y-10">

    {{-- TÍTULO --}}
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-extrabold bg-gradient-to-r from-yellow-500 to-yellow-300 bg-clip-text text-transparent">
            Agendamentos
        </h1>
    </div>

    {{-- FILTROS --}}
    @if($usuario->tipo !== 'cliente')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Barbearia (se admin) --}}
            @if($usuario->tipo === 'admin')
                <div class="bg-[#1a1410]/60 border border-yellow-500/20 rounded-xl p-4 backdrop-blur-md">
                    <label class="text-sm text-yellow-300/80 mb-1 block">Barbearia:</label>
                    <select onchange="window.location='?barbearia_id=' + this.value"
                            class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2
                                   text-[#f5e6d3] focus:ring-1 focus:ring-yellow-500/30">
                        @foreach($barbearias as $b)
                            <option value="{{ $b->id }}" {{ $barbeariaSelecionada->id == $b->id ? 'selected' : '' }}>
                                {{ $b->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Barber --}}
            <div class="bg-[#1a1410]/60 border border-yellow-500/20 rounded-xl p-4 backdrop-blur-md">
                <label class="text-sm text-yellow-300/80 mb-1 block">Barbeiro:</label>
                <select id="barbeiroSelect" onchange="atualizarFiltro()"
                        class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2 text-[#f5e6d3]">
                    @foreach($barbeiros as $b)
                        <option value="{{ $b->id }}" {{ ($barbeiroSelecionado && $barbeiroSelecionado->id == $b->id) ? 'selected' : '' }}>
                            {{ $b->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Data --}}
            <div class="bg-[#1a1410]/60 border border-yellow-500/20 rounded-xl p-4 backdrop-blur-md">
                <label class="text-sm text-yellow-300/80 mb-1 block">Data:</label>
                <input type="date" id="dataSelect" value="{{ $dataSelecionada }}" onchange="atualizarFiltro()"
                       class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2 text-[#f5e6d3]" />
            </div>

        </div>
    @endif

    {{-- HORÁRIOS DISPONÍVEIS --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6 gap-3">
    @forelse($horariosDisponiveis as $horario)
        @if($horario['ocupado'])
            <div class="p-4 rounded-xl backdrop-blur-md flex flex-col items-center justify-center cursor-not-allowed opacity-80 relative group overflow-visible
                @if($horario['status'] === 'concluido') border border-green-500/40 bg-green-500/10
                @elseif($horario['status'] === 'cancelado') border border-red-500/40 bg-red-500/10
                @else border border-yellow-500/40 bg-yellow-500/10 @endif">

                {{-- ÍCONE --}}
                @if($horario['status'] === 'concluido')
                    <i class="ph ph-check-circle text-green-400 text-lg mb-1"></i>
                @elseif($horario['status'] === 'cancelado')
                    <i class="ph ph-x-circle text-red-400 text-lg mb-1"></i>
                @else
                    <i class="ph pclockh- text-yellow-400 text-lg mb-1"></i>
                @endif

                {{-- HORA --}}
                <span class="text-sm">{{ $horario['hora'] }}</span>

                {{-- Tooltip --}}
                <div class="absolute -top-16 left-1/2 -translate-x-1/2
                            bg-[#1a1410] text-yellow-300 text-[13px] px-4 py-2 rounded-lg
                            border border-yellow-500/50 shadow-lg shadow-black/40 opacity-0
                            group-hover:opacity-100 transition-all whitespace-nowrap z-50 pointer-events-none">

                    {{ $horario['motivo'] }}

                    {{-- flechinha --}}
                    <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-3 h-3
                                bg-[#1a1410] border-l border-b border-yellow-500/50 rotate-45"></div>
                </div>
            </div>
        @else
            <div onclick="window.location='{{ route('agendamentos.create') }}?barbeiro_id={{ $barbeiroSelecionado->id }}&data_hora={{ $horario['dataHora'] }}'"
                class="p-4 rounded-xl bg-gradient-to-br from-yellow-600/40 to-yellow-300/40
                       backdrop-blur-lg border border-yellow-500/30 shadow-lg
                       text-[#1a1410] font-semibold text-center hover:scale-105
                       cursor-pointer transition transform flex flex-col items-center justify-center">
                <i class="ph ph-clock text-[#1a1410]/80 text-lg mb-1"></i>
                {{ $horario['hora'] }}
            </div>
        @endif
    @empty
        <div class="col-span-5 text-center text-yellow-300/70 py-4">Nenhum horário disponível</div>
    @endforelse
</div>

    
    </div>

</div>

<script>
function atualizarFiltro() {
    const barbeiro = document.getElementById('barbeiroSelect')?.value || '';
    const data = document.getElementById('dataSelect')?.value || '';
    const params = new URLSearchParams(window.location.search);
    if(barbeiro) params.set('barbeiro_id', barbeiro);
    if(data) params.set('data', data);
    window.location.search = params.toString();
}
</script>
@endsection
