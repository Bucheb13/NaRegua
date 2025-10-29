@extends('layouts.app')

@section('title', 'Agendamentos')

@section('content')
<div
  x-data="{
    // UI
    relatorioOpen: false,

    // Endpoint AJAX
    endpoint: '{{ url('/agendamentos/relatorio/load') }}',


    // IDs já existentes na página (se não existirem, ficam vazios)
    barbeiroId: '{{ $barbeiroSelecionado->id ?? '' }}',
    barbeariaId: '{{ $barbeariaSelecionada->id ?? '' }}',

    // Paginação
    page: 1,
    perPage: 20,
    loading: false,
    finished: false,

    // Filtros (ligados aos x-model dos inputs do modal)
    filtroCliente: '',
    openCliente: false,
resultadosCliente: [],
    filtroServico: '',
    filtroBarbeiro: '',
    filtroStatus: '',
    filtroData: '',
    filtroMes: '',
    filtroAno: '',

    abrirRelatorio() {
      this.relatorioOpen = true;
      this.$nextTick(() => {
        this.resetList();
        this.fetchPage();
        this.attachScroll();
      });
    },

    aplicarFiltros() {
      this.resetList();
      this.fetchPage();
    },

    limparFiltros() {
    this.filtroCliente = '';
    this.resultadosCliente = [];
    this.filtroServico = '';
    this.filtroStatus = '';
    this.filtroData = '';
    this.filtroMes = '';
    this.filtroAno = '';
    this.filtroBarbeiro = '';
    this.aplicarFiltros();
},

    resetList() {
      this.page = 1;
      this.finished = false;
      if (this.$refs.tbodyResultados) this.$refs.tbodyResultados.innerHTML = '';
      if (this.$refs.cardsResultados) this.$refs.cardsResultados.innerHTML = '';
    },

    async fetchPage() {
      if (this.loading || this.finished) return;
      this.loading = true;

      const params = new URLSearchParams({
        page: this.page,
        per_page: this.perPage,
        barbeiro_id: this.filtroBarbeiro || this.barbeiroId || '',
        barbearia_id: this.barbeariaId || '',
        cliente: this.filtroCliente || '',
        servico_id: this.filtroServico || '',
        status: this.filtroStatus || '',
        data: this.filtroData || '',
        mes: this.filtroMes || '',
        ano: this.filtroAno || '',
      });

      const resp = await fetch(`${this.endpoint}?${params.toString()}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });

      if (!resp.ok) {
        this.loading = false;
        return;
      }

      const json = await resp.json();

      // Insere os blocos de resultado nas respectivas áreas
      if (this.$refs.tbodyResultados && json.html_table) {
        this.$refs.tbodyResultados.insertAdjacentHTML('beforeend', json.html_table);
      }
      if (this.$refs.cardsResultados && json.html_cards) {
        this.$refs.cardsResultados.insertAdjacentHTML('beforeend', json.html_cards);
      }

      this.finished = !json.has_more;
      this.page++;
      this.loading = false;
    },

    attachScroll() {
      const el = this.$refs.scrollArea;
      if (!el) return;
      el.onscroll = () => {
        const nearBottom = el.scrollTop + el.clientHeight >= el.scrollHeight - 120;
        if (nearBottom) this.fetchPage();
      };
    },
    resultadoTimeout: null,

async buscarClientes() {
        clearTimeout(this.resultadoTimeout);
        this.resultadoTimeout = setTimeout(async () => {
            if (this.filtroCliente.length < 2) {
                this.resultadosCliente = [];
                return;
            }
            const resp = await fetch(`{{ route('agendamentos.clientes.search') }}?q=${this.filtroCliente}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                }
            });
            if (!resp.ok) return;
            const data = await resp.json();
            this.resultadosCliente = data;
        }, 300);
    },

    selecionarCliente(cliente) {
        this.filtroCliente = cliente.nome;
        this.resultadosCliente = [];
        this.openCliente = false;
},

  }"
  class="p-8 min-h-screen bg-gradient-to-b from-[#1a1410] to-[#2a1f1a] text-[#f5e6d3] space-y-10"
>



    {{-- TÍTULO --}}
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-extrabold bg-gradient-to-r from-yellow-500 to-yellow-300 bg-clip-text text-transparent">
            Agendamentos
        </h1>
{{-- BOTÃO RELATÓRIO --}}
<button
  @click="abrirRelatorio()"
  class="flex items-center gap-2 px-5 py-2 rounded-xl bg-gradient-to-r from-yellow-600/80 via-yellow-500/70 to-yellow-400/80
         text-[#1a1410] font-semibold shadow-lg shadow-yellow-500/20 hover:shadow-yellow-500/40 hover:scale-[1.02]
         transition transform duration-300"
>
  <i class="ph ph-chart-line text-lg"></i>
  Relatório
</button>


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
{{-- MODAL RELATÓRIO --}}
<div
    x-show="relatorioOpen"
    x-transition.opacity
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm"
>
    <div
        x-show="relatorioOpen"
        x-transition
        class="w-[95%] md:w-[90%] lg:w-[85%] xl:w-[80%] max-h-[95vh] overflow-y-auto
       bg-[#1a1410] border border-yellow-500/20 rounded-2xl p-6 shadow-2xl
       transform -translate-y-8"
    >

        {{-- CABEÇALHO --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold bg-gradient-to-r from-yellow-500 to-yellow-300 bg-clip-text text-transparent">
                Relatório de Agendamentos
            </h2>
            <button @click="relatorioOpen = false"
                class="text-yellow-300 hover:text-yellow-100 transition text-xl">
                <i class="ph ph-x-circle"></i>
            </button>
        </div>

        {{-- CONTEÚDO SERÁ INSERIDO NAS PRÓXIMAS ETAPAS --}}
        <div class="text-yellow-200/60">
            {{-- FILTROS DO RELATÓRIO --}}
<div class="space-y-6">

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">

      {{-- Cliente (autocomplete) --}}
<div class="flex flex-col relative" @click.outside="openCliente = false">
    <label class="text-sm text-yellow-200/80 mb-1">Cliente</label>

    <input
        type="text"
        x-model="filtroCliente"
        @input="buscarClientes(); openCliente = true"
        @focus="openCliente = true"
        placeholder="Buscar cliente..."
        class="bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-3 py-2 text-[#f5e6d3]
               focus:ring-1 focus:ring-yellow-400/40 focus:border-yellow-400/40 text-sm w-full"
    >

    {{-- Dropdown Cliente --}}
    <template x-if="openCliente && resultadosCliente.length > 0">
        <ul class="absolute top-full left-0 w-full mt-1 bg-[#1a1410] border border-yellow-500/30 rounded-lg shadow-lg
                   max-h-48 overflow-y-auto z-50">
            <template x-for="cliente in resultadosCliente" :key="cliente.id">
                <li @click="selecionarCliente(cliente)"
                    class="px-3 py-2 flex items-center gap-2 cursor-pointer hover:bg-yellow-500/10 text-sm text-[#f5e6d3]">
                    <i class="ph ph-user text-yellow-300"></i>
                    <span x-text="cliente.nome"></span>
                </li>
            </template>
        </ul>
    </template>
</div>


        
{{-- Profissional (Barbeiro) --}}
<div class="flex flex-col">
    <label class="text-sm text-yellow-200/80 mb-1">Profissional</label>
    <select
        x-model="filtroBarbeiro"
        class="bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-3 py-2 text-[#f5e6d3]
               focus:ring-1 focus:ring-yellow-400/40 focus:border-yellow-400/40 text-sm"
    >
        <option value="">Todos</option>
        @foreach($barbeiros as $bb)
            <option value="{{ $bb->id }}">{{ $bb->nome }}</option>
        @endforeach
    </select>
</div>

        {{-- Serviço --}}
        <div class="flex flex-col">
            <label class="text-sm text-yellow-200/80 mb-1">Serviço</label>
            <select
                x-model="filtroServico"
                class="bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-3 py-2 text-[#f5e6d3]
                       focus:ring-1 focus:ring-yellow-400/40 focus:border-yellow-400/40 text-sm"
            >
                <option value="">Todos</option>
                @foreach($servicos as $s)
                    <option value="{{ $s->id }}">{{ $s->nome }}</option>
                @endforeach
            </select>
        </div>

        {{-- Status --}}
        <div class="flex flex-col">
            <label class="text-sm text-yellow-200/80 mb-1">Status</label>
            <select
                x-model="filtroStatus"
                class="bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-3 py-2 text-[#f5e6d3]
                       focus:ring-1 focus:ring-yellow-400/40 focus:border-yellow-400/40 text-sm"
            >
                <option value="">Todos</option>
                <option value="agendado">Agendado</option>
                <option value="concluido">Concluído</option>
                <option value="cancelado">Cancelado</option>
            </select>
        </div>

        {{-- Data específica --}}
        <div class="flex flex-col">
            <label class="text-sm text-yellow-200/80 mb-1">Data Específica</label>
            <input
                type="date"
                x-model="filtroData"
                class="bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-3 py-2 text-[#f5e6d3]
                       focus:ring-1 focus:ring-yellow-400/40 focus:border-yellow-400/40 text-sm"
            >
        </div>

        {{-- Mês --}}
        <div class="flex flex-col">
            <label class="text-sm text-yellow-200/80 mb-1">Mês</label>
            <select
                x-model="filtroMes"
                class="bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-3 py-2 text-[#f5e6d3]
                       focus:ring-1 focus:ring-yellow-400/40 focus:border-yellow-400/40 text-sm"
            >
                <option value="">Todos</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}">{{ $m }}</option>
                @endfor
            </select>
        </div>

        {{-- Ano --}}
        <div class="flex flex-col">
            <label class="text-sm text-yellow-200/80 mb-1">Ano</label>
            <select
                x-model="filtroAno"
                class="bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-3 py-2 text-[#f5e6d3]
                       focus:ring-1 focus:ring-yellow-400/40 focus:border-yellow-400/40 text-sm"
            >
                <option value="">Todos</option>
                @foreach($anosAgendamentos as $ano)
                    <option value="{{ $ano }}">{{ $ano }}</option>
                @endforeach
            </select>
        </div>

    </div>

    {{-- BOTÕES --}}
<div class="flex justify-end gap-3 pt-4">
    {{-- Limpar --}}
    <button
        @click="limparFiltros()"
        class="px-4 py-2 text-sm rounded-lg border border-yellow-500/40 text-yellow-300 hover:bg-yellow-500/10 transition"
    >
        Limpar
    </button>

    {{-- Exportar (PDF futuramente) --}}
    <button
        disabled
        class="px-4 py-2 text-sm rounded-lg bg-yellow-500/30 text-yellow-100 opacity-50 cursor-not-allowed"
    >
        Exportar PDF (em breve)
    </button>

    {{-- Aplicar filtros --}}
    <button
        @click="aplicarFiltros()"
        class="px-6 py-2 rounded-lg bg-gradient-to-r from-yellow-600 via-yellow-500 to-yellow-400
               text-[#1a1410] font-semibold shadow-lg hover:scale-[1.02] transition"
    >
        Aplicar
    </button>
</div> <!-- ← FECHA O CONTÊINER DOS BOTÕES AQUI -->

{{-- DIVISÓRIA --}}
<div class="border-t border-yellow-500/20 my-4"></div>

{{-- ÁREA SCROLLÁVEL (INFINITE SCROLL) --}}
<div x-ref="scrollArea" class="max-h-[60vh] overflow-y-auto pr-1">

    {{-- TABELA (DESKTOP) --}}
    <div class="hidden md:block">
        <table class="w-full text-sm">
            <thead class="sticky top-0 bg-[#1a1410] border-b border-yellow-500/20">
                <tr class="text-yellow-200/80">
                    <th class="text-left py-3">Cliente</th>
                    <th class="text-left py-3">Serviço</th>
                    <th class="text-left py-3">Data</th>
                    <th class="text-left py-3">Hora</th>
                    <th class="text-right py-3">Valor</th>
                    <th class="text-center py-3">Status</th>
                </tr>
            </thead>
            <tbody x-ref="tbodyResultados" class="align-top"></tbody>
        </table>
    </div>

    {{-- CARDS (MOBILE) --}}
    <div x-ref="cardsResultados" class="md:hidden grid grid-cols-1 gap-3 py-2"></div>

    {{-- LOADING / FIM --}}
    <div class="py-4 text-center">
        <template x-if="loading">
            <div class="text-yellow-300/70 text-sm">Carregando...</div>
        </template>
        <template x-if="!loading && finished">
            <div class="text-yellow-300/50 text-xs">— Fim dos resultados —</div>
        </template>
    </div>

</div>


</div>

        </div>

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
