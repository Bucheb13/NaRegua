<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>NaRegua - @yield('title')</title>
    
        
        
        {{-- Chart.js --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
        <style>[x-cloak]{ display:none !important; }</style>
    
        {{-- CSS & JS bundlados --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])



<script src="https://unpkg.com/@phosphor-icons/web"></script>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>


    </head>
    
    @php
    use App\Models\Barbearia;

    $usuario = auth()->user();
    $barbeariaTopo = null;

    if (isset($barbeariaSelecionada) && $barbeariaSelecionada instanceof Barbearia) {
        // Quando admin seleciona no filtro
        $barbeariaTopo = $barbeariaSelecionada;
    } elseif ($usuario) {
        // Para barbeiro e cliente -> garantir objeto
        $barbeariaTopo = $usuario->barbearia()->first();
    }

    // fallback se ainda assim vier null
    if (!$barbeariaTopo instanceof Barbearia) {
        $barbeariaTopo = null;
    }
@endphp

@php
    // valores iniciais vindos do Blade
    $nomeInit     = $barbeariaTopo->nome ?? '';
    $emailInit    = $barbeariaTopo->email ?? '';
    $respInit     = $barbeariaTopo->responsavel_nome ?? '';
    $telInit      = $barbeariaTopo->telefone ?? '';
    $endInit      = $barbeariaTopo->endereco ?? '';
    $logoUrlInit  = isset($barbeariaTopo->logo) ? Storage::url($barbeariaTopo->logo) : null;
    $isAdmin      = ($usuario->tipo === 'admin');
@endphp

<body
  x-data="{
      // MODALs
      openNovaBarbearia: false,
      openEditBarbearia: false,
      openSemBarbearia: false,
      openConfirmExcluir: false,

      // Live state (topo)
      barbeariaNome: '{{ $nomeInit }}',      // LITERAL (como você pediu)
      barbeariaEmail: '{{ $emailInit }}',
      barbeariaResp: '{{ $respInit }}',
      barbeariaTel:  '{{ $telInit }}',
      barbeariaEnd:  '{{ $endInit }}',

      // Logo live (topo)
      barbeariaLogoUrl: {{ $logoUrlInit ? "'$logoUrlInit'" : 'null' }},
      barbeariaLogoTemp: null,   // preview temporário

        // Preview temporario do logo na criação
      previewLogo: null,

      // Ao fechar modal de edição, reverter o preview do logo
      onCloseEdit() {
        this.openEditBarbearia = false;
        // Reverter logo do topo se havia preview temporário
        this.barbeariaLogoTemp = null;
      },

      // src da imagem no topo (usa temp se houver; senão original)
      logoSrc() {
        return this.barbeariaLogoTemp ?? this.barbeariaLogoUrl;
      }
  }"
  @keydown.escape="onCloseEdit()"
  class="bg-[#1a1410] text-[#f5e6d3] font-sans antialiased min-h-screen flex">



  <!-- Sidebar -->
  <aside x-data="{ open: true }"
         id="sidebar"
         class="hidden md:flex flex-col w-72 bg-[#1a1410]/70 backdrop-blur-xl border-r border-yellow-500/20 text-yellow-200 transition-all duration-300 overflow-hidden">

{{-- TOPO COM LOGO OU INICIAIS --}}
<div class="px-6 pt-8 pb-6 flex flex-col items-center text-center">
    <template x-if="logoSrc()">
        <img :src="logoSrc()"
             class="w-20 h-20 object-cover rounded-full shadow-[0_0_12px_rgba(212,175,55,0.35)] border border-yellow-500/30 mb-3 cursor-pointer"
             @click="{{ $barbeariaTopo ? 'openEditBarbearia = true' : 'openSemBarbearia = true' }}">
    </template>

    <template x-if="!logoSrc()">
        @php
            $iniciais = $barbeariaTopo
                ? collect(explode(' ', trim($barbeariaTopo->nome)))->filter()->map(fn($p) => mb_substr($p,0,1))->take(2)->implode('')
                : 'NR';
        @endphp
        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-yellow-500 to-yellow-300 flex items-center justify-center text-[#1a1410] text-2xl font-extrabold shadow-[0_0_12px_rgba(255,215,0,0.35)] mb-3 cursor-pointer"
             @click="{{ $barbeariaTopo ? 'openEditBarbearia = true' : 'openSemBarbearia = true' }}">
            {{ $iniciais }}
        </div>
    </template>

    <div class="text-lg font-bold text-yellow-200">
        <span x-text="barbeariaNome || 'Selecione uma Barbearia'"></span>
    </div>
</div>



      <nav class="flex-1 px-4 mt-4 space-y-2">
          <a href="{{ route('dashboard') }}"
             class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 hover:bg-yellow-400/10 hover:text-yellow-200
             @if(request()->routeIs('dashboard')) bg-yellow-400/20 text-yellow-100 shadow-[0_0_14px_rgba(212,175,55,0.22)] @endif">
              <i class="ph ph-chart-bar text-lg"></i>
              <span>Dashboard</span>
          </a>
          <a href="{{ route('agendamentos.index') }}"
             class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 hover:bg-yellow-400/10 hover:text-yellow-200
             @if(request()->routeIs('agendamentos.*')) bg-yellow-400/20 text-yellow-100 shadow-[0_0_14px_rgba(212,175,55,0.22)] @endif">
              <i class="ph ph-calendar-check text-lg"></i>
              <span>Agendamentos</span>
          </a>
          <a href="{{ route('vendas.index') }}"
             class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 hover:bg-yellow-400/10 hover:text-yellow-200
             @if(request()->routeIs('vendas.*')) bg-yellow-400/20 text-yellow-100 shadow-[0_0_14px_rgba(212,175,55,0.22)] @endif">
              <i class="ph ph-currency-circle-dollar text-lg"></i>
              <span>Vendas</span>
          </a>
          <a href="{{ route('servicos.index') }}"
             class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 hover:bg-yellow-400/10 hover:text-yellow-200
             @if(request()->routeIs('servicos.*')) bg-yellow-400/20 text-yellow-100 shadow-[0_0_14px_rgba(212,175,55,0.22)] @endif">
              <i class="ph ph-scissors text-lg"></i>
              <span>Serviços</span>
          </a>
          <a href="{{ route('produtos.index') }}"
             class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 hover:bg-yellow-400/10 hover:text-yellow-200
             @if(request()->routeIs('produtos.*')) bg-yellow-400/20 text-yellow-100 shadow-[0_0_14px_rgba(212,175,55,0.22)] @endif">
              <i class="ph ph-bottle text-lg"></i>
              <span>Produtos</span>
          </a>
          <a href="{{ route('usuarios.index') }}"
             class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 hover:bg-yellow-400/10 hover:text-yellow-200
             @if(request()->routeIs('usuarios.*')) bg-yellow-400/20 text-yellow-100 shadow-[0_0_14px_rgba(212,175,55,0.22)] @endif">
              <i class="ph ph-users text-lg"></i>
              <span>Usuários</span>
          </a>
          
          
      </nav>

      <div class="p-4 text-center text-xs text-yellow-300/60 border-t border-yellow-500/20">
          © {{ date('Y') }} NaRegua
      </div>
  </aside>

  <!-- Main Wrapper -->
  <div class="flex-1 flex flex-col min-h-screen">
      <!-- Header -->
      <header class="sticky top-0 z-30 backdrop-blur-xl bg-white/3 border-b border-yellow-500/5 px-6 py-3 flex justify-between items-center">
        <div class="flex items-center gap-3">
            {{-- Mobile --}}
            <button class="md:hidden text-yellow-300"
                    onclick="document.getElementById('sidebar').classList.toggle('hidden')">
                <i class="ph ph-list text-2xl"></i>
            </button>
        </div>
    
        <div class="flex items-center gap-4">
    
            {{-- BOTÃO NOVA BARBEARIA (apenas ADMIN) --}}
            @if($usuario->tipo === 'admin')
    <button
        @click="openNovaBarbearia = true"
        class="rounded-full px-4 py-1.5 text-xs font-medium bg-yellow-500/20 hover:bg-yellow-500/30
               text-yellow-200 border border-yellow-500/30 flex items-center gap-1 transition">
        <i class="ph ph-building-office"></i> + Nova Barbearia
    </button>
@endif

    
            {{-- Usuário --}}
            <span class="flex items-center gap-2 text-yellow-200/90 text-sm">
                <i class="ph ph-user-circle text-lg"></i>
                {{ $usuario->nome ?? 'Usuário' }}
            </span>
    
            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="rounded-full px-4 py-1.5 text-xs font-medium bg-white/5 hover:bg-white/10
                               text-yellow-200/90 border border-yellow-500/10 transition-all">
                    <i class="ph ph-sign-out mr-1"></i> Sair
                </button>
            </form>
        </div>
    </header>
    
    
    
      <!-- Conteúdo -->
      <main class="flex-1 p-6 overflow-y-auto">
          @yield('content')

          {{-- IMPORTANTE: scripts por página aqui dentro --}}
          @stack('scripts')
      </main>
  </div>
  <!-- MODAL NOVA BARBEARIA -->
<div
x-show="openNovaBarbearia"
x-cloak
class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
x-transition.opacity
>

<div
    @click.away="openNovaBarbearia = false"
    class="bg-[#1a1410] border border-yellow-500/30 rounded-2xl shadow-xl w-full max-w-2xl p-6 relative"
    x-transition.scale
>
    <!-- Título -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-bold text-yellow-300 flex items-center gap-2">
            <i class="ph ph-buildings"></i> Nova Barbearia
        </h2>

        <button @click="openNovaBarbearia = false" class="text-yellow-200 hover:text-yellow-400">
            <i class="ph ph-x text-xl"></i>
        </button>
    </div>

    <form action="{{ route('barbearias.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-yellow-200/70">Nome da Barbearia *</label>
                <input type="text" name="nome" required
                    class="w-full bg-[#241b16]/60 border border-yellow-500/30 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="text-sm text-yellow-200/70">Responsável *</label>
                <input type="text" name="responsavel_nome" required
                    class="w-full bg-[#241b16]/60 border border-yellow-500/30 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="text-sm text-yellow-200/70">CNPJ</label>
                <input type="text" name="cnpj"
                    class="w-full bg-[#241b16]/60 border border-yellow-500/30 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="text-sm text-yellow-200/70">Telefone</label>
                <input type="text" name="telefone"
                    class="w-full bg-[#241b16]/60 border border-yellow-500/30 rounded-lg px-3 py-2 text-sm">
            </div>

            <div class="md:col-span-2">
                <label class="text-sm text-yellow-200/70">Email</label>
                <input type="email" name="email"
                    class="w-full bg-[#241b16]/60 border border-yellow-500/30 rounded-lg px-3 py-2 text-sm">
            </div>

            <div class="md:col-span-2">
                <label class="text-sm text-yellow-200/70">Endereço</label>
                <input type="text" name="endereco"
                    class="w-full bg-[#241b16]/60 border border-yellow-500/30 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="text-sm text-yellow-200/70">Validade da Licença</label>
                <input type="date" name="licenca_validade"
                    class="w-full bg-[#241b16]/60 border border-yellow-500/30 rounded-lg px-3 py-2 text-sm">
            </div>

            <!-- Upload Logo -->
            <div class="md:col-span-2">
                <label class="text-sm text-yellow-200/70 mb-1 block">Logo</label>
                <input type="file" name="logo" accept="image/*"
                @change="() => { if (typeof previewLogo !== 'undefined') { previewLogo = URL.createObjectURL($event.target.files[0]); } }"


                       class="block w-full text-sm text-yellow-200">
                
                       <template x-if="typeof previewLogo !== 'undefined' && previewLogo">
                    <img :src="previewLogo" class="mt-3 w-24 h-24 rounded-lg object-cover border border-yellow-500/30 shadow">
                </template>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <button type="button" @click="openNovaBarbearia = false"
                    class="px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-yellow-200 border border-yellow-500/20">
                Cancelar
            </button>

            <button type="submit"
                    class="px-4 py-2 rounded-lg bg-gradient-to-r from-yellow-600 via-yellow-500 to-yellow-400 text-[#1a1410] font-semibold shadow-lg hover:shadow-xl transition">
                Salvar
            </button>
        </div>
    </form>

</div>
</div>
@php
    $isAdmin = ($usuario->tipo === 'admin');
@endphp

@php $isAdminView = ($usuario->tipo === 'admin'); @endphp

@if($barbeariaTopo)
<!-- MODAL EDITAR BARBEARIA -->
<div x-show="openEditBarbearia" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" x-transition.opacity>
  <div @click.away="onCloseEdit()"
       class="bg-[#1a1410] border border-yellow-500/30 rounded-2xl shadow-xl w-full max-w-2xl p-6 relative"
       x-transition.scale>
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-bold text-yellow-300 flex items-center gap-2">
        <i class="ph ph-buildings"></i> Editar Barbearia
      </h2>
      <button @click="onCloseEdit()" class="text-yellow-200 hover:text-yellow-400">
        <i class="ph ph-x text-xl"></i>
      </button>
    </div>

    <form method="POST" action="{{ route('barbearias.update', $barbeariaTopo->id) }}" enctype="multipart/form-data" class="space-y-4"
          @submit="">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- CAMPOS VISÍVEIS PARA AMBOS (barbeiro e admin) --}}
        <div class="md:col-span-2">
          <label class="text-sm text-yellow-200/70">Nome da Barbearia *</label>
          <input type="text" name="nome" required
                 x-model="barbeariaNome"
                 value="{{ old('nome', $barbeariaTopo->nome) }}"
                 class="w-full bg-[#241b16]/60 border border-yellow-500/30 rounded-lg px-3 py-2 text-sm">
        </div>

        <div class="md:col-span-2">
          <label class="text-sm text-yellow-200/70">Responsável *</label>
          <input type="text" name="responsavel_nome" required
                 x-model="barbeariaResp"
                 value="{{ old('responsavel_nome', $barbeariaTopo->responsavel_nome) }}"
                 class="w-full bg-[#241b16]/60 border border-yellow-500/30 rounded-lg px-3 py-2 text-sm">
        </div>

        <div>
          <label class="text-sm text-yellow-200/70">Telefone</label>
          <input type="text" name="telefone"
                 x-model="barbeariaTel"
                 value="{{ old('telefone', $barbeariaTopo->telefone) }}"
                 class="w-full bg-[#241b16]/60 border border-yellow-500/30 rounded-lg px-3 py-2 text-sm">
        </div>

        <div>
          <label class="text-sm text-yellow-200/70">Email</label>
          <input type="email" name="email"
                 x-model="barbeariaEmail"
                 value="{{ old('email', $barbeariaTopo->email) }}"
                 class="w-full bg-[#241b16]/60 border border-yellow-500/30 rounded-lg px-3 py-2 text-sm">
        </div>

        <div class="md:col-span-2">
          <label class="text-sm text-yellow-200/70">Endereço</label>
          <input type="text" name="endereco"
                 x-model="barbeariaEnd"
                 value="{{ old('endereco', $barbeariaTopo->endereco) }}"
                 class="w-full bg-[#241b16]/60 border border-yellow-500/30 rounded-lg px-3 py-2 text-sm">
        </div>

        {{-- CAMPOS SOMENTE PARA ADMIN (HIDE para barbeiro) --}}
        @if($isAdminView)
          <div>
            <label class="text-sm text-yellow-200/70">CNPJ</label>
            <input type="text" name="cnpj"
                   value="{{ old('cnpj', $barbeariaTopo->cnpj) }}"
                   class="w-full bg-[#241b16]/60 border border-yellow-500/30 rounded-lg px-3 py-2 text-sm">
          </div>

          <div>
            <label class="text-sm text-yellow-200/70">Validade da Licença</label>
            <input type="date" name="licenca_validade"
                   value="{{ old('licenca_validade', optional($barbeariaTopo->licenca_validade)->format('Y-m-d')) }}"
                   class="w-full bg-[#241b16]/60 border border-yellow-500/30 rounded-lg px-3 py-2 text-sm">
          </div>
        @endif

        {{-- LOGO (ambos podem) --}}
        <div class="md:col-span-2">
          <label class="text-sm text-yellow-200/70 mb-1 block">Logo</label>

          <div class="flex items-center gap-4">
            @if($barbeariaTopo->logo)
              <img src="{{ Storage::url($barbeariaTopo->logo) }}"
                   class="w-16 h-16 rounded-lg object-cover border border-yellow-500/30 shadow">
            @endif

            <template x-if="barbeariaLogoTemp">
              <img :src="barbeariaLogoTemp" class="w-16 h-16 rounded-lg object-cover border border-yellow-500/30 shadow">
            </template>
          </div>

          <input type="file" name="logo" accept="image/*"
                 @change="
                    if ($event.target.files[0]) {
                      const url = URL.createObjectURL($event.target.files[0]);
                      // Preview imediato: modal + topo
                      barbeariaLogoTemp = url;
                    } else {
                      barbeariaLogoTemp = null;
                    }
                 "
                 class="mt-2 block w-full text-sm text-yellow-200">
        </div>
      </div>

      <div class="flex justify-between items-center pt-4">
        @if($isAdminView)
            <button type="button"
                    @click="openConfirmExcluir = true"
                    class="px-4 py-2 rounded-lg border border-red-400/40 text-red-300 hover:text-red-200 hover:bg-red-500/10 transition flex items-center gap-2">
                <i class="ph ph-warning-circle text-lg"></i>
                Excluir Barbearia
            </button>
        @endif
    
        <div class="flex gap-3">
            <button type="button" @click="onCloseEdit()"
                    class="px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-yellow-200 border border-yellow-500/20">
                Cancelar
            </button>
            <button type="submit"
                    class="px-4 py-2 rounded-lg bg-gradient-to-r from-yellow-600 via-yellow-500 to-yellow-400 text-[#1a1410] font-semibold shadow-lg hover:shadow-xl transition">
                Salvar
            </button>
        </div>
    </div>
    </form>
  </div>
</div>
@endif


<!-- MINI MODAL - Sem Barbearia Selecionada -->
<div x-show="openSemBarbearia" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" x-transition.opacity>
    <div class="bg-[#1a1410] border border-yellow-500/30 rounded-2xl shadow-xl w-full max-w-md p-6" x-transition.scale>
      <h3 class="text-lg font-bold text-yellow-300 mb-3">Atenção</h3>
      <p class="text-yellow-100/90">Nenhuma barbearia selecionada. Selecione uma barbearia para editar.</p>
      <div class="mt-5 text-right">
        <button @click="openSemBarbearia = false"
                class="px-4 py-2 rounded-lg bg-gradient-to-r from-yellow-600 via-yellow-500 to-yellow-400 text-[#1a1410] font-semibold shadow hover:shadow-lg">
          OK
        </button>
      </div>
    </div>
  </div>
  
  @if($barbeariaTopo)
  <!-- FULL MODAL - Confirmar Exclusão -->
  <div x-show="openConfirmExcluir" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black/70 backdrop-blur-sm" x-transition.opacity>
    <div @click.away="openConfirmExcluir = false"
         class="bg-[#1a1410] border border-red-500/30 rounded-2xl shadow-2xl w-full max-w-2xl p-6"
         x-transition.scale>
      <div class="flex items-start gap-3">
        <div class="shrink-0">
          <i class="ph ph-warning-octagon text-4xl text-red-400"></i>
        </div>
        <div class="flex-1">
          <h3 class="text-xl font-extrabold text-red-300">Excluir barbearia DEFINITIVAMENTE</h3>
          <p class="mt-2 text-yellow-100/90">
            Esta ação é <span class="text-red-300 font-semibold">irreversível</span>. Todos os dados relacionados à barbearia
            <span class="text-yellow-300 font-semibold" x-text="barbeariaNome"></span> serão removidos:
            usuários, agendamentos, vendas, itens de venda, serviços, produtos e a própria barbearia.
          </p>
          <p class="mt-1 text-yellow-100/70">Confirme para prosseguir.</p>
        </div>
      </div>
  
      <form method="POST" action="{{ route('barbearias.destroy', $barbeariaTopo->id) }}" class="mt-6">
        @csrf
        @method('DELETE')
  
        <div class="flex justify-end gap-3">
          <button type="button"
                  @click="openConfirmExcluir = false"
                  class="px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-yellow-200 border border-yellow-500/20">
            Cancelar
          </button>
          <button type="submit"
                  class="px-4 py-2 rounded-lg bg-red-600/90 hover:bg-red-600 text-white font-semibold shadow-lg hover:shadow-xl transition flex items-center gap-2">
            <i class="ph ph-trash-simple"></i>
            Excluir definitivamente
          </button>
        </div>
      </form>
    </div>
  </div>
  @endif
  

</body>
</html>
