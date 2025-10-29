@extends('layouts.app')

@section('title', 'Usuários')

@section('content')
{{-- Phosphor Icons --}}
<script src="https://unpkg.com/@phosphor-icons/web"></script>

{{-- Animações custom (stagger, sweep, pulse) --}}
<style>
  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  .fade-in-up { animation: fadeInUp .55s ease both; }

  /* Linha luminosa (LS3-C): varredura + respiração */
  @keyframes sweep {
    0% { transform: translateX(-100%); opacity: 0; }
    40% { opacity: .8; }
    100% { transform: translateX(200%); opacity: 0; }
  }
  @keyframes breath {
    0%,100% { opacity: .18; }
    50% { opacity: .35; }
  }
  .sweep::before {
    content: "";
    position: absolute;
    top: -1px;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.7), transparent);
    animation: sweep 1.6s linear infinite;
}

  .sweep::after {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 1rem;
    box-shadow: inset 0 0 0 1px rgba(255, 230, 180, .25);
    opacity: .2;
    animation: breath 3.4s ease-in-out infinite;
    pointer-events: none;
  }

  /* Popover base */
  .popover {
    transform-origin: top right;
    transition: opacity .18s ease, transform .18s ease;
  }
  .popover[data-open="false"] { opacity: 0; transform: scale(.96); pointer-events: none; }
  .popover[data-open="true"]  { opacity: 1; transform: scale(1); }
</style>

<div class="p-8 min-h-screen bg-gradient-to-b from-[#1a1410] to-[#2a1f1a] text-[#f5e6d3] space-y-8">

  {{-- Cabeçalho --}}
  <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
    <div>
      <h1 class="text-3xl md:text-4xl font-extrabold bg-gradient-to-r from-yellow-600 via-yellow-500 to-yellow-300 bg-clip-text text-transparent">
        Usuários
      </h1>
      <p class="text-yellow-300/70 mt-2">Gerencie clientes, barbeiros e administradores</p>
    </div>

    @if($usuarioLogado->tipo !== 'cliente')
      <a href="{{ route('usuarios.create') }}"
         class="group inline-flex items-center gap-2 rounded-full px-5 py-2.5
                bg-gradient-to-r from-yellow-500 to-yellow-400 text-[#1a1410] font-semibold
                shadow-[0_0_12px_rgba(212,175,55,0.25)]
                hover:shadow-[0_0_24px_rgba(255,255,255,0.45)]
                transition-all">
        <i class="ph ph-user-plus text-lg translate-x-0 group-hover:translate-x-0.5 transition-transform"></i>
        Novo Usuário
      </a>
    @endif
  </div>

  {{-- Filtro de barbearia (somente admin) --}}
  @if($usuarioLogado->tipo === 'admin')
    <div class="bg-[#1a1410]/50 border border-yellow-500/20 rounded-xl p-4 backdrop-blur-md w-fit">
        <label for="barbearia" class="text-sm text-yellow-300/80 block mb-1">Filtrar por Barbearia:</label>

        <select id="barbearia"
                class="bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-4 py-2 text-[#f5e6d3]
                       focus:ring-1 focus:ring-yellow-500/30"
                onchange="window.location='?barbearia_id=' + this.value">

            <option value="" {{ empty($barbeariaSelecionada) ? 'selected' : '' }}>Todas</option>

            @foreach($barbearias as $b)
            <option value="{{ $b->id }}"
              {{ ((is_object($barbeariaSelecionada) && $barbeariaSelecionada->id == $b->id) || (!is_object($barbeariaSelecionada) && $barbeariaSelecionada == $b->id)) ? 'selected' : '' }}>
              {{ $b->nome }}
          </option>
          
            @endforeach
        </select>
    </div>
@endif


  {{-- Mensagem de sucesso --}}
  @if(session('success'))
    <div class="rounded-xl border border-green-500/20 bg-green-500/10 text-green-200 px-4 py-3 shadow">
      {{ session('success') }}
    </div>
  @endif

  {{-- Lista (cards por linha) --}}
  <div class="space-y-4">
    @forelse($usuarios as $usuario)
      @php
        $nome = $usuario->nome ?? '';
        $iniciais = collect(preg_split('/\s+/', trim($nome)))->filter()->slice(0,2)->map(function($p){ return mb_strtoupper(mb_substr($p,0,1)); })->implode('');
        // Badge dourada premium (BC2): variações leves
        $badgeStyle = 'border border-yellow-400/40 text-yellow-200/90 bg-yellow-300/5 shadow-[inset_0_0_8px_rgba(255,230,180,0.18)]';
        $tipoLabel = ucfirst($usuario->tipo);
      @endphp

        <div class="relative sweep overflow-hidden fade-in-up"
            style="animation-delay: {{ $loop->index * 70 }}ms;">
        <div class="group flex items-center gap-4 rounded-2xl p-4 md:p-5
                bg-[#1a1410]/60 backdrop-blur-md border border-yellow-500/20
                shadow-[0_0_12px_rgba(212,175,55,0.18)]
                hover:shadow-[0_0_26px_rgba(255,255,255,0.45)]
                transition-all duration-300">



          {{-- Avatar (AVR1 redondo com gradiente dourado) --}}
          <div class="relative shrink-0 w-14 h-14 md:w-16 md:h-16 rounded-full
                      bg-gradient-to-br from-yellow-400 to-yellow-600
                      flex items-center justify-center text-[#1a1410] font-extrabold text-lg md:text-xl
                      shadow-[0_0_10px_rgba(255,215,0,0.35)]">
            {{ $iniciais ?: 'US' }}
          </div>

          {{-- Informações principais --}}
          <div class="flex-1 min-w-0">
            <div class="flex flex-col md:flex-row md:items-center md:gap-3">
              <h3 class="text-lg md:text-xl font-semibold text-[#f5e6d3] truncate">
                {{ $usuario->nome }}
              </h3>
              {{-- Badge tipo (BC2) --}}
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs md:text-[13px]
                           {{ $badgeStyle }}">
                <i class="ph ph-shield-star text-[14px] opacity-80"></i>
                {{ $tipoLabel }}
              </span>
            </div>
            <div class="mt-1 text-yellow-300/70 text-sm">
              {{ $usuario->email }}
            </div>
            <div class="mt-1 text-yellow-300/60 text-xs md:text-sm">
              Barbearia: <span class="text-yellow-200">{{ $usuario->barbearia->nome ?? '-' }}</span>
            </div>
          </div>

          {{-- Ações (BTN-B: ícone move levemente no hover) --}}
          <div class="flex items-center gap-2 md:gap-3">
            @if($usuarioLogado->tipo === 'admin' || ($usuarioLogado->tipo === 'barbeiro' && $usuario->barbearia_id === $usuarioLogado->barbearia_id))
              <a href="{{ route('usuarios.edit', $usuario->id) }}"
                 class="group/action inline-flex items-center gap-2 rounded-full px-3.5 py-2
                        bg-yellow-500/90 text-[#1a1410] font-semibold
                        hover:bg-yellow-400 transition-all">
                <i class="ph ph-pencil-line text-[18px] translate-x-0 group-hover/action:translate-x-0.5 transition-transform"></i>
                <span class="hidden sm:inline">Editar</span>
              </a>
            @endif

            @if($usuarioLogado->tipo === 'admin')
              <div class="relative" data-popover>
                <button type="button"
                        class="group/action inline-flex items-center gap-2 rounded-full px-3.5 py-2
                               bg-red-500/90 text-white font-semibold
                               hover:bg-red-400 transition-all"
                        data-popover-toggle>
                  <i class="ph ph-trash text-[18px] translate-x-0 group-hover/action:-translate-x-0.5 transition-transform"></i>
                  <span class="hidden sm:inline">Deletar</span>
                </button>

                {{-- Popover de confirmação (CONF3) --}}
                <div class="popover absolute right-0 mt-2 z-10 w-60 rounded-xl p-4
                            bg-[#1a1410]/95 border border-yellow-500/20
                            shadow-[0_8px_24px_rgba(0,0,0,0.45)]
                            text-[#f5e6d3]" data-open="false">
                  <div class="text-sm font-semibold mb-2">Confirmar exclusão</div>
                  <p class="text-xs text-yellow-300/80 mb-3">
                    Tem certeza que deseja deletar <span class="text-yellow-200 font-semibold">{{ $usuario->nome }}</span>?
                  </p>
                  <div class="flex items-center justify-end gap-2">
                    <button type="button"
                            class="px-3 py-1.5 rounded-full text-xs bg-white/5 text-yellow-200 border border-white/10 hover:bg-white/10"
                            data-popover-cancel>Cancelar</button>

                    <form method="POST" action="{{ route('usuarios.destroy', $usuario->id) }}" class="inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                              class="px-3 py-1.5 rounded-full text-xs bg-red-500/90 text-white hover:bg-red-400">
                        Confirmar
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    @empty
      <div class="rounded-2xl border border-yellow-500/20 bg-[#1a1410]/60 backdrop-blur-md p-8 text-center text-yellow-300/70">
        Nenhum usuário encontrado.
      </div>
    @endforelse
  </div>

  {{-- Paginação (opcional) --}}
  @if(method_exists($usuarios, 'links'))
    <div class="pt-4">
      {{ $usuarios->links() }}
    </div>
  @endif
</div>
@endsection

@push('scripts')
<script>
  // Popover inline simples (CONF3)
  document.addEventListener('click', (e) => {
    // Fecha popovers ao clicar fora
    document.querySelectorAll('[data-popover] .popover').forEach(p => p.dataset.open = 'false');

    const btn = e.target.closest('[data-popover-toggle]');
    if (!btn) return;

    const wrap = btn.closest('[data-popover]');
    const pop = wrap.querySelector('.popover');
    pop.dataset.open = (pop.dataset.open === 'true') ? 'false' : 'true';

    // impedir fechamento imediato (fora)
    e.stopPropagation();
  });

  document.addEventListener('click', (e) => {
    const isInside = e.target.closest('[data-popover]');
    if (!isInside) {
      document.querySelectorAll('[data-popover] .popover').forEach(p => p.dataset.open = 'false');
    }
  });

  document.querySelectorAll('[data-popover] [data-popover-cancel]')
    .forEach(b => b.addEventListener('click', (e) => {
      const pop = e.target.closest('.popover');
      pop.dataset.open = 'false';
    }));
</script>
@endpush
