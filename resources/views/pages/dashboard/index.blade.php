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
<div 
    x-data="{
        open:false,
        selecionadaId: {{ $barbeariaSelecionada?->id ?? 'null' }},
        barbearias: [
            @foreach($barbearias as $b)
            {
                id: {{ $b->id }},
                nome: @js($b->nome),
                logo: @js($b->logo ? Storage::url($b->logo) : null),
                iniciais: @js(collect(explode(' ', trim($b->nome)))->filter()->map(fn($p) => mb_substr($p,0,1))->take(2)->implode('')),
            },
            @endforeach
        ],
        atual() {
            return this.barbearias.find(b => b.id === this.selecionadaId) || null;
        },
        selecionar(id) {
            this.selecionadaId = id;
            this.open = false;
            window.location = '?barbearia_id=' + (id ?? '');
        }
    }"
    class="bg-[#1a1410]/60 backdrop-blur-md border border-yellow-500/20 rounded-2xl p-6 shadow-lg w-full md:w-auto relative z-[100]"
>
    <label class="block text-sm text-yellow-300/80 mb-2">Selecionar Barbearia:</label>

    <!-- Botão -->
    <button type="button"
            @click="open = !open"
            class="w-80 flex items-center justify-between gap-3 bg-[#241b16]/60 border border-yellow-500/30 rounded-xl px-4 py-3 text-left hover:border-yellow-400/40 transition relative z-30">
        <div class="flex items-center gap-3">
            <template x-if="atual() && atual().logo">
                <img :src="atual().logo" class="w-7 h-7 rounded-full object-cover border border-yellow-500/30">
            </template>
            <template x-if="!atual() || !atual().logo">
                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-yellow-500 to-yellow-300 text-[#1a1410] text-xs font-extrabold flex items-center justify-center border border-yellow-500/30"
                     x-text="atual() ? atual().iniciais : 'RB'"></div>
            </template>
            <span class="text-[#f5e6d3] text-sm" x-text="atual() ? atual().nome : 'Todas as Barbearias'"></span>
        </div>
        <i class="ph ph-caret-down text-yellow-300/80"></i>
    </button>

    <!-- DROPDOWN - agora flutuante -->
    <div x-show="open" x-transition.opacity x-cloak
         class="absolute z-50 w-80 max-h-72 overflow-auto rounded-xl border border-yellow-500/30 bg-[#1a1410] shadow-2xl mt-1">
        <!-- Todas -->
        <button type="button" @click="selecionar(null)"
                class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-yellow-500/10">
            <div class="w-7 h-7 rounded-full bg-yellow-500/20 border border-yellow-500/30"></div>
            <span class="text-sm text-yellow-200/90">Todas as Barbearias</span>
        </button>
        <div class="border-t border-yellow-500/10 my-1"></div>
        <!-- Lista -->
        <template x-for="b in barbearias" :key="b.id">
            <button type="button" @click="selecionar(b.id)"
                    class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-yellow-500/10">
                <template x-if="b.logo">
                    <img :src="b.logo" class="w-7 h-7 rounded-full object-cover border border-yellow-500/30">
                </template>
                <template x-if="!b.logo">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-yellow-500 to-yellow-300 text-[#1a1410] text-xs font-extrabold flex items-center justify-center border border-yellow-500/30"
                         x-text="b.iniciais"></div>
                </template>
                <span class="text-sm text-yellow-100/95" x-text="b.nome"></span>
            </button>
        </template>
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
            @php
    function formatarDuracao($minutos) {
        $h = floor($minutos / 60);
        $m = $minutos % 60;
        if ($h > 0 && $m > 0) return "{$h}h {$m}min";
        if ($h > 0) return "{$h}h";
        return "{$m}min";
    }
@endphp

            @forelse($proximosAgendamentos as $ag)
    @php
        $nomeCli = $ag->cliente?->nome ?? 'Cliente';
        $iniciais = collect(explode(' ', trim($nomeCli)))->filter()->map(fn($p) => mb_substr($p,0,1))->take(2)->implode('');
        $telefone = preg_replace('/\D/', '', $ag->cliente?->telefone ?? '');
        $iso = \Carbon\Carbon::parse($ag->data_hora)->toIso8601String();
    @endphp

    <div 
        x-data="agCard({data: '{{ $iso }}', status: '{{ $ag->status }}'})"
        class="flex items-center justify-between gap-6 p-4 rounded-xl bg-white/5 backdrop-blur-xl border border-yellow-500/20 shadow-[0_0_18px_rgba(255,199,44,0.08)] hover:shadow-[0_0_30px_rgba(255,199,44,0.28)] transition-all duration-500"
    >

        {{-- ESQUERDA --}}
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-500 to-yellow-300 text-[#1a1410] flex items-center justify-center font-extrabold border border-yellow-500/30">
                {{ $iniciais }}
            </div>
            <div class="flex flex-col leading-tight">
                <span class="font-semibold text-yellow-300">{{ $nomeCli }}</span>
                <span class="text-xs text-yellow-200/70">
                    {{ $ag->servico?->nome ?? 'Serviço' }}
                    @if($ag->servico?->duracao_minutos)
                        • {{ formatarDuracao($ag->servico->duracao_minutos) }}
                    @endif
                </span>
                
            </div>
        </div>

        {{-- MEIO --}}
        <div class="flex flex-col items-center text-center text-xs min-w-[120px]">
            <div class="px-3 py-1 rounded-full bg-yellow-500/10 border border-yellow-500/20 text-yellow-200"
                 x-text="labelTempo"></div>
            <div class="mt-1 flex items-center gap-1"
                 :class="classeStatus">
                <i :class="iconeStatus"></i>
                <span x-text="textoStatus"></span>
            </div>
        </div>

        {{-- DIREITA --}}
        <div class="flex items-center gap-2">

            {{-- WhatsApp --}}
            <div x-data="tooltip('📞 Conversar com cliente')" @mouseenter="toggle(true)" @mouseleave="toggle(false)" class="relative">
                <a href="https://wa.me/55{{ $telefone }}" target="_blank"
                   class="p-2 rounded-lg bg-green-600/80 hover:bg-green-500 transition flex items-center justify-center text-xs font-semibold">
                   <i class="ph ph-whatsapp-logo text-lg"></i>
                </a>
                <div x-show="show" x-transition
                     class="absolute -top-9 left-1/2 -translate-x-1/2 whitespace-nowrap px-3 py-1 rounded-xl bg-[#1a1410]/90 border border-yellow-500/30 text-yellow-300 text-[10px] shadow-lg">
                    {{ '📞 Conversar' }}
                    <div class="absolute bottom-[-4px] left-1/2 -translate-x-1/2 w-2 h-2 bg-[#1a1410]/90 rotate-45 border-r border-b border-yellow-500/30"></div>
                </div>
            </div>

            {{-- BOTÕES SE NAO CONCLUIDO/CANCELADO --}}
            @if($ag->status === 'agendado' || $ag->status === 'pendente')
                {{-- Confirmar --}}
                <div x-data="tooltip('✅ Concluir atendimento')" @mouseenter="toggle(true)" @mouseleave="toggle(false)" class="relative">
                    <form method="POST" action="{{ route('agendamentos.confirmar', $ag->id) }}">
                        @csrf
                        <button class="p-2 rounded-lg bg-green-700/70 hover:bg-green-600 flex items-center justify-center">
                            <i class="ph ph-check text-green-200 text-sm"></i>
                        </button>
                    </form>
                    <div x-show="show" x-transition
                         class="absolute -top-9 left-1/2 -translate-x-1/2 whitespace-nowrap px-3 py-1 rounded-xl bg-[#1a1410]/90 border border-yellow-500/30 text-yellow-300 text-[10px] shadow-lg">
                        ✅ Concluir
                        <div class="absolute bottom-[-4px] left-1/2 -translate-x-1/2 w-2 h-2 bg-[#1a1410]/90 rotate-45 border-r border-b border-yellow-500/30"></div>
                    </div>
                </div>

                {{-- Cancelar --}}
                <div x-data="tooltip('❌ Cancelar agendamento')" @mouseenter="toggle(true)" @mouseleave="toggle(false)" class="relative">
                    <form method="POST" action="{{ route('agendamentos.cancelar', $ag->id) }}">
                        @csrf
                        <button class="p-2 rounded-lg bg-red-700/70 hover:bg-red-600 flex items-center justify-center">
                            <i class="ph ph-x text-red-200 text-sm"></i>
                        </button>
                    </form>
                    <div x-show="show" x-transition
                         class="absolute -top-9 left-1/2 -translate-x-1/2 whitespace-nowrap px-3 py-1 rounded-xl bg-[#1a1410]/90 border border-yellow-500/30 text-yellow-300 text-[10px] shadow-lg">
                        ❌ Cancelar
                        <div class="absolute bottom-[-4px] left-1/2 -translate-x-1/2 w-2 h-2 bg-[#1a1410]/90 rotate-45 border-r border-b border-yellow-500/30"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@empty
    <div class="text-yellow-300/70 text-center">Nenhum agendamento futuro.</div>
@endforelse



        </div>
    </div>

    {{-- ========================= GRÁFICOS (TABS) ========================= --}}
<div class="w-full rounded-2xl shadow-2xl border border-yellow-400/30 bg-[#1a1410]/50 backdrop-blur-2xl
            ring-1 ring-yellow-400/20 overflow-hidden">
    {{-- Tabs header --}}
    <div class="flex items-center justify-between gap-4 flex-wrap px-6 pt-6">
        <div class="inline-flex items-center rounded-full bg-[#241b16]/70 border border-yellow-500/20 p-1 shadow-inner shadow-yellow-500/10">
            <button type="button" data-tab-target="#tab-receita"
                class="tab-pill active px-4 py-2 text-sm rounded-full transition
                       bg-yellow-500/20 text-yellow-300 border border-yellow-400/30 shadow-inner">
                Receita Mensal
            </button>
            <button type="button" data-tab-target="#tab-agenda"
                class="tab-pill px-4 py-2 text-sm rounded-full transition hover:bg-yellow-500/10 text-yellow-200/80">
                Agendamentos
            </button>
        </div>
        <span class="text-[12px] text-yellow-300/60 pr-6">Dica: use as abas para alternar entre os gráficos</span>
    </div>

    {{-- Conteúdo das abas --}}
    <div class="mt-5 space-y-6 px-6 pb-6">
        <div id="tab-receita" class="tab-pane block">
            <h3 class="text-lg font-bold text-yellow-400 mb-3 flex items-center gap-2">
                <i class="ph ph-currency-circle-dollar text-yellow-400 text-xl"></i>
                Receita mensal
            </h3>
            <div class="relative w-full rounded-xl border border-yellow-500/20 bg-white/5 backdrop-blur-xl shadow-lg p-3" style="height: 360px;">
                <canvas id="receitasChart"></canvas>
            </div>
            <p class="mt-3 text-xs text-yellow-300/70">
                Barra: <span class="font-semibold">Produtos</span> •
                Linhas: <span class="font-semibold">Serviços (concluídos)</span> e <span class="font-semibold">Total</span>
            </p>
        </div>

        <div id="tab-agenda" class="tab-pane hidden">
            <h3 class="text-lg font-bold text-yellow-400 mb-3 flex items-center gap-2">
                <i class="ph ph-calendar-blank text-yellow-400 text-xl"></i>
                Agendamentos por Status (empilhado)
            </h3>
            <div class="relative w-full rounded-xl border border-yellow-500/20 bg-white/5 backdrop-blur-xl shadow-lg p-3" style="height: 360px;">
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
    /** Contagem regressiva detalhada e dinâmica (atualiza a cada 30s) */
    (function(){
        function formatDiff(ms) {
            if (ms <= 0) return 'Começando agora';
            const totalSec = Math.floor(ms / 1000);
            const days = Math.floor(totalSec / 86400);
            const hours = Math.floor((totalSec % 86400) / 3600);
            const mins = Math.floor((totalSec % 3600) / 60);
    
            const parts = [];
            if (days) parts.push(days + (days === 1 ? ' dia' : ' dias'));
            if (hours) parts.push(hours + (hours === 1 ? ' h' : ' h'));
            if (mins && days === 0) parts.push(mins + (mins === 1 ? ' min' : ' min'));
    
            return 'Faltam ' + (parts.length ? parts.join(' e ') : 'menos de 1 min');
        }
    
        function updateCountdowns() {
            document.querySelectorAll('[data-countdown][data-when]').forEach(el => {
                const when = new Date(el.getAttribute('data-when')).getTime();
                const now  = Date.now();
                const diff = when - now;
                el.textContent = diff > 0 ? formatDiff(diff) : 'Já ocorreu';
            });
        }
    
        updateCountdowns();
        setInterval(updateCountdowns, 30 * 1000);
    })();
    </script>
    

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

<script>
    function agCard({data, status}) {
        return {
            dataHora: new Date(data),
            status,
            labelTempo: '',
            textoStatus: '',
            iconeStatus: '',
            classeStatus: '',
            init() {
                this.atualizar();
                setInterval(() => this.atualizar(), 60000);
            },
            atualizar() {
                const agora = new Date();
                const diffMs = this.dataHora - agora;
                const diffAbs = Math.abs(diffMs);
                const diffMin = Math.floor(diffAbs / 60000);
                const diffH   = Math.floor(diffMin / 60);
                const diffMinRest = diffMin % 60;
    
                const tempoFmt = (diffH > 0 ? `${diffH}h ` : '') + `${diffMinRest}min`;
    
                // FUTURO
                if (diffMs > 0) {
                    this.labelTempo = `Faltam ${tempoFmt}`;
                    this.textoStatus = (this.status === 'concluido') ? 'Concluído' :
                                       (this.status === 'cancelado') ? 'Cancelado' :
                                       'Agendado';
                }
                // PASSADO DENTRO DA JANELA (2h)
                else if (diffAbs <= 2 * 60 * 60 * 1000) {
                    if (this.status === 'concluido') {
                        this.labelTempo = `Concluído há ${tempoFmt}`;
                    } else if (this.status === 'cancelado') {
                        this.labelTempo = `Cancelado há ${tempoFmt}`;
                    } else {
                        this.labelTempo = `Atrasado há ${tempoFmt}`;
                    }
                    this.textoStatus = (this.status === 'concluido') ? 'Concluído' :
                                       (this.status === 'cancelado') ? 'Cancelado' :
                                       'Atrasado';
                }
    
                // DEFINE ICONE / CORES
                if (this.textoStatus === 'Concluído') {
                    this.iconeStatus = 'ph ph-check-circle text-green-400';
                    this.classeStatus = 'text-green-400';
                } else if (this.textoStatus === 'Cancelado') {
                    this.iconeStatus = 'ph ph-x-circle text-red-400';
                    this.classeStatus = 'text-red-400';
                } else {
                    this.iconeStatus = 'ph ph-clock text-yellow-300';
                    this.classeStatus = 'text-yellow-200';
                }
            }
        }
    }
    </script>

<script>
    function tooltip(text) {
        return {
            show: false,
            text,
            toggle(v = true) { this.show = v; }
        }
    }
    </script>
    
    
@endsection
