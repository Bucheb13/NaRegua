<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>NaRegua - @yield('title')</title>
    
        
        
        {{-- Chart.js --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
        <style>[x-cloak]{ display:none !important; }</style>
    
        {{-- CSS & JS bundlados --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])



<script src="https://unpkg.com/@phosphor-icons/web"></script>

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


<body class="bg-[#1a1410] text-[#f5e6d3] font-sans antialiased min-h-screen flex">
  <!-- Sidebar -->
  <aside x-data="{ open: true }"
         id="sidebar"
         class="hidden md:flex flex-col w-72 bg-[#1a1410]/70 backdrop-blur-xl border-r border-yellow-500/20 text-yellow-200 transition-all duration-300 overflow-hidden">

      {{-- TOPO COM LOGO OU INICIAIS --}}
      <div class="px-6 pt-8 pb-6 flex flex-col items-center text-center">
          @if($barbeariaTopo && $barbeariaTopo->logo)
              <img src="{{ Storage::url($barbeariaTopo->logo) }}"
                   class="w-20 h-20 object-cover rounded-full shadow-[0_0_12px_rgba(212,175,55,0.35)] border border-yellow-500/30 mb-3">
          @else
              @php
                  $iniciais = $barbeariaTopo
                      ? collect(explode(' ', trim($barbeariaTopo->nome)))->filter()->map(fn($p) => mb_substr($p,0,1))->take(2)->implode('')
                      : 'NR';
              @endphp
              <div class="w-20 h-20 rounded-full bg-gradient-to-br from-yellow-500 to-yellow-300 flex items-center justify-center text-[#1a1410] text-2xl font-extrabold shadow-[0_0_12px_rgba(255,215,0,0.35)] mb-3">
                  {{ $iniciais }}
              </div>
          @endif

          <div class="text-lg font-bold text-yellow-200">
              {{ $barbeariaTopo->nome ?? 'Selecione uma Barbearia' }}
          </div>
      </div>

      <nav class="flex-1 px-4 mt-4 space-y-2">
          <a href="{{ route('dashboard') }}"
             class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 hover:bg-yellow-400/10 hover:text-yellow-200
             @if(request()->routeIs('dashboard')) bg-yellow-400/20 text-yellow-100 shadow-[0_0_14px_rgba(212,175,55,0.22)] @endif">
              <i class="ph ph-chart-bar text-lg"></i>
              <span>Dashboard</span>
          </a>
          <a href="{{ route('usuarios.index') }}"
             class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-300 hover:bg-yellow-400/10 hover:text-yellow-200
             @if(request()->routeIs('usuarios.*')) bg-yellow-400/20 text-yellow-100 shadow-[0_0_14px_rgba(212,175,55,0.22)] @endif">
              <i class="ph ph-users text-lg"></i>
              <span>Usuários</span>
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
            <span class="flex items-center gap-2 text-yellow-200/90 text-sm">
                <i class="ph ph-user-circle text-lg"></i>
                {{ $usuario->nome ?? 'Usuário' }}
            </span>
    
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
</body>
</html>
