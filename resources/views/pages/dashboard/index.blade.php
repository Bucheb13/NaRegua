@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
{{-- ======================= DASHBOARD ======================= --}}

<div class="p-8 min-h-screen bg-gradient-to-b from-[#1a1410] to-[#2a1f1a] text-[#f5e6d3] space-y-10">

    {{-- Cabeçalho --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h1 class="text-4xl font-extrabold bg-gradient-to-r from-yellow-600 via-yellow-500 to-yellow-400 bg-clip-text text-transparent">
                Painel de Controle
            </h1>
            <p class="text-yellow-300/70 mt-2">Visão geral do seu negócio em tempo real</p>
        </div>

        <div class="flex items-center gap-3 bg-[#2a1f1a]/60 backdrop-blur-md px-5 py-3 rounded-xl border border-yellow-500/20 shadow-lg">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
            <span class="text-sm text-[#f5e6d3]">Sistema Online</span>
        </div>

        {{-- Filtro de Barbearia (admin) --}}
        @if($usuario->tipo === 'admin')
            <div class="bg-[#1a1410]/60 backdrop-blur-md border border-yellow-500/20 rounded-2xl p-6 shadow-lg">
                <div class="flex flex-col md:flex-row md:items-center gap-4 md:gap-6 justify-between">
                    <div class="flex-1">
                        <label class="block text-sm text-yellow-300/80 mb-1">Selecionar Barbearia:</label>
                        <select onchange="window.location='?barbearia_id=' + this.value"
                                class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2.5 text-[#f5e6d3] focus:ring-1 focus:ring-yellow-500/30">
                            <option value="">Todas as Barbearias</option>
                            @foreach($barbearias as $b)
                                <option value="{{ $b->id }}" {{ ($barbeariaSelecionada?->id ?? null) == $b->id ? 'selected' : '' }}>
                                    {{ $b->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- CARDS PRINCIPAIS --}}
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    @php
        // Se o controller novo foi aplicado, teremos: $totalVendas, $totalProdutos, $totalServicos, $totalUsuarios
        $cards = [
            ['titulo'=>'Receita do Mês (Produtos + Serviços)','valor'=>'R$ '.number_format($totalVendas ?? 0,2,',','.'),'icone'=>'ph-currency-circle-dollar','gradiente'=>'from-yellow-500/20 to-yellow-400/20'],
            ['titulo'=>'Produtos Vendidos','valor'=>'R$ '.number_format($totalProdutos ?? 0,2,',','.'),'icone'=>'ph-basket','gradiente'=>'from-yellow-400/20 to-yellow-300/20'],
            ['titulo'=>'Serviços Concluídos','valor'=>'R$ '.number_format($totalServicos ?? 0,2,',','.'),'icone'=>'ph-scissors','gradiente'=>'from-yellow-300/20 to-yellow-200/20'],
            ['titulo'=>'Clientes Ativos','valor'=>$totalUsuarios ?? 0,'icone'=>'ph-users-three','gradiente'=>'from-yellow-200/20 to-yellow-500/20'],
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($cards as $card)
        <div class="group relative overflow-hidden rounded-2xl p-6 bg-[#1a1410]/70 backdrop-blur-md
                    border border-yellow-500/20 shadow-[0_0_12px_rgba(212,175,55,0.18)]
                    transition-all duration-500 hover:shadow-[0_0_28px_rgba(255,255,255,0.55)]
                    hover:scale-[1.04] hover:-translate-y-1">
            <div class="pointer-events-none absolute -inset-1 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                 style="background: radial-gradient(80% 60% at 80% 0%, rgba(255,255,255,0.35) 0%, rgba(255,255,255,0) 60%);"></div>
            <div class="absolute inset-0 bg-gradient-to-br {{ $card['gradiente'] }} opacity-25"></div>
            <i class="absolute top-4 right-4 text-4xl text-yellow-300/80 group-hover:text-yellow-200 transition-colors duration-300 {{ $card['icone'] }}"></i>
            <div class="relative z-10 flex flex-col justify-end h-full">
                <div class="text-3xl md:text-4xl font-extrabold text-[#f5e6d3] tracking-tight">{{ $card['valor'] }}</div>
                <div class="text-yellow-300/80 text-xs md:text-sm uppercase mt-2 font-semibold tracking-wide">{{ $card['titulo'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- LISTA: Próximos Agendamentos --}}
    <div class="bg-[#1a1410]/50 backdrop-blur-md border border-yellow-500/20 rounded-2xl p-6 shadow-lg">
        <h3 class="text-xl font-semibold text-yellow-500 mb-4">Próximos Agendamentos</h3>
        <div class="space-y-4">
            @forelse($proximosAgendamentos as $ag)
                <div class="relative p-6 rounded-2xl bg-white/5 backdrop-blur-xl border border-yellow-500/20 
                            shadow-[0_0_18px_rgba(255,199,44,0.08)] hover:shadow-[0_0_35px_rgba(255,199,44,0.35)]
                            transition-all duration-500 flex flex-col sm:flex-row items-center justify-between gap-6">

                    {{-- Bloco Esquerdo (Ícone + Info) --}}
                    <div class="flex items-center gap-4 w-full sm:w-auto justify-center">
                        <div class="text-4xl text-yellow-400/90">
                            <i class="ph ph-calendar-check"></i>
                        </div>

                        <div class="text-center sm:text-left">
                            <div class="text-lg font-semibold text-yellow-300">
                                {{ $ag->cliente?->nome ?? 'Cliente não informado' }}
                            </div>
                            <div class="text-sm text-yellow-200/70 mt-1">
                                {{ \Carbon\Carbon::parse($ag->data_hora)->format('d/m/Y H:i') }}
                                • {{ $ag->servico?->nome ?? 'Serviço' }}
                            </div>

                            {{-- STATUS --}}
                            <div class="mt-2 text-xs font-semibold flex justify-center sm:justify-start">
                                @if($ag->status === 'agendado' || $ag->status === 'pendente')
                                    <span class="px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded-lg">Pendente</span>
                                @elseif($ag->status === 'concluido')
                                    <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded-lg">Concluído</span>
                                @elseif($ag->status === 'cancelado')
                                    <span class="px-2 py-1 bg-red-500/20 text-red-400 rounded-lg">Cancelado</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Botões --}}
                    @if($ag->status === 'agendado' || $ag->status === 'pendente')
                        <div class="flex flex-col sm:flex-row gap-2 justify-center">
                            <form method="POST" action="{{ route('agendamentos.confirmar', $ag->id) }}">
                                @csrf
                                <button
                                    class="px-4 py-2 text-xs rounded-lg bg-green-600/80 hover:bg-green-500 transition font-semibold">
                                    Confirmar
                                </button>
                            </form>

                            <form method="POST" action="{{ route('agendamentos.cancelar', $ag->id) }}">
                                @csrf
                                <button
                                    class="px-4 py-2 text-xs rounded-lg bg-red-600/80 hover:bg-red-500 transition font-semibold">
                                    Cancelar
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-yellow-300/70 text-center">Nenhum agendamento futuro.</div>
            @endforelse
        </div>
    </div>

    {{-- ========================= GRÁFICOS (TABS) ========================= --}}
    <div class="w-full bg-[#1a1410]/60 border border-yellow-500/20 backdrop-blur-md p-6 rounded-2xl shadow-lg">

        {{-- Tabs - estilo pill premium --}}
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="inline-flex items-center rounded-full bg-[#241b16]/70 border border-yellow-500/20 p-1">
                <button
                    type="button"
                    data-tab-target="#tab-receita"
                    class="tab-pill active px-4 py-2 text-sm rounded-full transition
                           bg-yellow-500/20 text-yellow-300 border border-yellow-400/30 shadow-inner"
                >
                    Receita Mensal
                </button>
                <button
                    type="button"
                    data-tab-target="#tab-agenda"
                    class="tab-pill px-4 py-2 text-sm rounded-full transition
                           hover:bg-yellow-500/10 text-yellow-200/80"
                >
                    Agendamentos
                </button>
            </div>

            <span class="text-[12px] text-yellow-300/60">Dica: use as abas para alternar entre os gráficos</span>
        </div>

        {{-- Conteúdo das abas --}}
        <div class="mt-5 space-y-6">

            {{-- ABA: Receita --}}
            <div id="tab-receita" class="tab-pane block">
                <h3 class="text-lg font-bold text-yellow-400 mb-3 flex items-center gap-2">
                    <i class="ph ph-currency-circle-dollar text-yellow-400 text-xl"></i>
                    Receita mensal
                </h3>

                <div class="relative w-full" style="height: 340px;">
                    <canvas id="receitasChart"></canvas>
                </div>

                <p class="mt-3 text-xs text-yellow-300/70">
                    Barra: <span class="font-semibold">Produtos</span> •
                    Linhas: <span class="font-semibold">Serviços (concluídos)</span> e <span class="font-semibold">Total</span>
                </p>
            </div>

            {{-- ABA: Agendamentos --}}
            <div id="tab-agenda" class="tab-pane hidden">
                <h3 class="text-lg font-bold text-yellow-400 mb-3 flex items-center gap-2">
                    <i class="ph ph-calendar-blank text-yellow-400 text-xl"></i>
                    Agendamentos por Status (empilhado)
                </h3>

                <div class="relative w-full" style="height: 340px;">
                    <canvas id="agendamentosChart"></canvas>
                </div>
            </div>

        </div>
    </div>
    {{-- ======================= FIM GRÁFICOS (TABS) ======================= --}}

</div> {{-- Fim container principal --}}

{{-- Chart.js 4 --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // ===== Helpers =====
    const toBRL = v => {
        try { return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(v||0)); }
        catch { return `R$ ${Number(v||0).toFixed(2).replace('.', ',')}`; }
    };
    const monthNowIdx = (new Date()).getMonth(); // 0..11

    // ===== Dados do backend (controller atualizado) =====
    const meses      = @json($vendasMensais['meses'] ?? []);
    const produtos   = @json($vendasMensais['produtos'] ?? []);
    const servicos   = @json($vendasMensais['servicos'] ?? []); // serviços concluídos
    const agPend     = @json($agendamentosMensais['pendentes'] ?? []);
    const agConc     = @json($agendamentosMensais['concluidos'] ?? []);
    const agCanc     = @json($agendamentosMensais['cancelados'] ?? []);
    const agTotal    = @json($agendamentosMensais['total'] ?? []);

    // Total de receita (produtos + serviços) calculado no front (se necessário)
    const receitaTotal = meses.map((_, i) => Number(produtos[i] || 0) + Number(servicos[i] || 0));

    // ===== Tabs (pills) =====
    const btns = document.querySelectorAll('.tab-pill');
    const panes = document.querySelectorAll('.tab-pane');
    btns.forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.getAttribute('data-tab-target');

            btns.forEach(b => b.classList.remove('active','bg-yellow-500/20','text-yellow-300','border','border-yellow-400/30','shadow-inner'));
            btn.classList.add('active','bg-yellow-500/20','text-yellow-300','border','border-yellow-400/30','shadow-inner');

            panes.forEach(p => p.classList.add('hidden'));
            const pane = document.querySelector(target);
            if (pane) {
                pane.classList.remove('hidden');
                pane.classList.add('block');
            }

            // Ajusta tamanho dos gráficos ao trocar aba
            if (target === '#tab-receita' && window.__receitaChart) window.__receitaChart.resize();
            if (target === '#tab-agenda'  && window.__agendaChart)  window.__agendaChart.resize();
        });
    });

    // ===== Gráfico: Receita (bar + 2 lines) =====
    (function(){
        const el = document.getElementById('receitasChart');
        if (!el) return;

        const colorBarProdutos = 'rgba(212,175,55,0.45)';
        const colorBarBorder   = '#d4af37';
        const colorLineServ    = '#ffde8a';
        const colorFillServ    = 'rgba(255, 222, 138, 0.12)';
        const colorLineTotal   = '#ffd04d';
        const colorFillTotal   = 'rgba(255, 208, 77, 0.10)';

        window.__receitaChart = new Chart(el, {
            data: {
                labels: meses,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Produtos',
                        data: produtos,
                        backgroundColor: produtos.map((_, i) => i === monthNowIdx ? 'rgba(255, 208, 77, 0.65)' : colorBarProdutos),
                        borderColor: produtos.map((_, i) => i === monthNowIdx ? '#ffd04d' : colorBarBorder),
                        borderWidth: 1,
                        borderRadius: 6,
                        order: 3
                    },
                    {
                        type: 'line',
                        label: 'Serviços (concluídos)',
                        data: servicos,
                        borderColor: colorLineServ,
                        backgroundColor: colorFillServ,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3.5,
                        pointHoverRadius: 5,
                        pointBackgroundColor: servicos.map((_, i) => i === monthNowIdx ? '#ffde8a' : '#e6c36b'),
                        order: 2
                    },
                    {
                        type: 'line',
                        label: 'Total',
                        data: receitaTotal,
                        borderColor: colorLineTotal,
                        backgroundColor: colorFillTotal,
                        fill: false,
                        tension: 0.25,
                        pointRadius: 3.5,
                        pointHoverRadius: 5,
                        pointBackgroundColor: receitaTotal.map((_, i) => i === monthNowIdx ? '#ffd04d' : '#daa520'),
                        order: 1
                    },
                ]
            },
            options: {
                maintainAspectRatio: false,
                animation: { duration: 300 },
                plugins: {
                    legend: { labels: { color: '#c9a961' } },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const v = Number(ctx.parsed.y ?? ctx.raw ?? 0);
                                return ` ${ctx.dataset.label}: ${toBRL(v)}`;
                            },
                            afterBody: (items) => {
                                const idx = items?.[0]?.dataIndex ?? -1;
                                if (idx < 0) return '';
                                const p = Number(produtos[idx]||0);
                                const s = Number(servicos[idx]||0);
                                const t = Number(receitaTotal[idx]||0);
                                return [
                                    '──────────────',
                                    `Produtos: ${toBRL(p)}`,
                                    `Serviços: ${toBRL(s)}`,
                                    `Total: ${toBRL(t)}`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#c9a961' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(201,169,97,0.15)' },
                        ticks: {
                            color: '#c9a961',
                            callback: (value) => toBRL(value)
                        }
                    }
                }
            }
        });
    })();

    // ===== Gráfico: Agendamentos (stacked bars + total line) =====
    (function(){
        const el = document.getElementById('agendamentosChart');
        if (!el) return;

        const barPend = 'rgba(255, 215, 0, 0.45)';   // amarelo
        const barConc = 'rgba(0, 200, 120, 0.45)';   // verde
        const barCanc = 'rgba(255, 99, 132, 0.45)';  // vermelho
        const lineTot = '#ffd04d';

        window.__agendaChart = new Chart(el, {
            data: {
                labels: meses,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Pendentes',
                        data: agPend,
                        backgroundColor: agPend.map((_, i) => i === monthNowIdx ? 'rgba(255, 215, 0, 0.70)' : barPend),
                        borderColor: 'rgba(255, 215, 0, 0.9)',
                        borderWidth: 1,
                        borderRadius: 6,
                        stack: 'agenda'
                    },
                    {
                        type: 'bar',
                        label: 'Concluídos',
                        data: agConc,
                        backgroundColor: agConc.map((_, i) => i === monthNowIdx ? 'rgba(0, 200, 120, 0.70)' : barConc),
                        borderColor: 'rgba(0, 200, 120, 0.9)',
                        borderWidth: 1,
                        borderRadius: 6,
                        stack: 'agenda'
                    },
                    {
                        type: 'bar',
                        label: 'Cancelados',
                        data: agCanc,
                        backgroundColor: agCanc.map((_, i) => i === monthNowIdx ? 'rgba(255, 99, 132, 0.70)' : barCanc),
                        borderColor: 'rgba(255, 99, 132, 0.9)',
                        borderWidth: 1,
                        borderRadius: 6,
                        stack: 'agenda'
                    },
                    {
                        type: 'line',
                        label: 'Total de Agendamentos',
                        data: agTotal,
                        borderColor: lineTot,
                        tension: 0.25,
                        fill: false,
                        pointRadius: 3.5,
                        pointHoverRadius: 5,
                        pointBackgroundColor: agTotal.map((_, i) => i === monthNowIdx ? '#ffd04d' : '#daa520'),
                        order: 0
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                animation: { duration: 300 },
                plugins: {
                    legend: { labels: { color: '#c9a961' } },
                    tooltip: {
                        callbacks: {
                            // Mostra valores e proporções
                            afterBody: (items) => {
                                const i = items?.[0]?.dataIndex ?? -1;
                                if (i < 0) return '';
                                const p = Number(agPend[i]||0);
                                const c = Number(agConc[i]||0);
                                const x = Number(agCanc[i]||0);
                                const t = Number(agTotal[i]||0) || (p+c+x);
                                const pct = v => t ? ` (${Math.round(v*100/t)}%)` : '';
                                return [
                                    '──────────────',
                                    `Pendentes: ${p}${pct(p)}`,
                                    `Concluídos: ${c}${pct(c)}`,
                                    `Cancelados: ${x}${pct(x)}`,
                                    `Total: ${t}`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { color: '#c9a961' }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        grid: { color: 'rgba(201,169,97,0.15)' },
                        ticks: { color: '#c9a961' }
                    }
                }
            }
        });
    })();
});
</script>
@endsection
