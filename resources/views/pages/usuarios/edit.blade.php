@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
{{-- Phosphor Icons --}}
<script src="https://unpkg.com/@phosphor-icons/web"></script>

{{-- Estilos/Animações pontuais --}}
<style>
  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  .fade-in-up { animation: fadeInUp .5s ease both; }

  /* micro-heading premium (SEC3) */
  .micro-heading {
    position: relative;
    padding-left: 12px;
  }
  .micro-heading::before {
    content: "";
    position: absolute;
    left: 0; top: 50%;
    transform: translateY(-50%);
    width: 6px; height: 6px; border-radius: 9999px;
    background: radial-gradient(circle, #ffe6b4 0%, #f5d98a 60%, transparent 100%);
    box-shadow: 0 0 10px rgba(245, 217, 138, .7);
  }

  /* Badge BC2 base */
  .badge-gold {
    border: 1px solid rgba(255, 215, 130, .35);
    color: rgba(255, 240, 200, .95);
    background: rgba(255, 230, 170, .06);
    box-shadow: inset 0 0 8px rgba(255,230,180,.18);
  }

  /* Reveal senha (REVEAL3: slide + glow) */
  .reveal-wrap {
    overflow: hidden;
    transition: grid-template-rows .35s ease, opacity .35s ease, filter .35s ease;
    display: grid;
    grid-template-rows: 0fr;
    opacity: 0;
    filter: brightness(.96);
  }
  .reveal-wrap[data-open="true"] {
    grid-template-rows: 1fr;
    opacity: 1;
    filter: brightness(1);
  }
  .reveal-inner {
    min-height: 0; /* necessário para o grid-auto */
  }
  .reveal-glow {
    transition: box-shadow .35s ease, border-color .35s ease, background-color .35s ease;
    box-shadow: 0 0 0 rgba(255,255,255,0);
    border-color: rgba(212,175,55,.2);
    background-color: rgba(26,20,16,.55);
  }
  .reveal-wrap[data-open="true"] .reveal-glow {
    box-shadow: 0 0 20px rgba(255,255,255,.18);
    border-color: rgba(255,215,130,.35);
    background-color: rgba(26,20,16,.65);
  }
</style>

<div class="p-8 min-h-screen bg-[#1a1410]/10 backdrop-blur-sm text-[#f5e6d3] space-y-10">

  {{-- Banner Premium (H3) --}}
  <div class="rounded-2xl overflow-hidden border border-yellow-500/20 bg-gradient-to-r from-[#221811] via-[#2a1f1a] to-[#221811] shadow-[0_0_18px_rgba(212,175,55,0.25)]">
    <div class="px-6 py-8 md:px-8 md:py-10 relative">
      <div class="absolute inset-0 opacity-20 pointer-events-none" style="background: radial-gradient(100% 60% at 100% 0%, rgba(255,255,255,0.35) 0%, rgba(255,255,255,0) 60%);"></div>
      <div class="flex items-center gap-4">
        <i class="ph ph-user-gear text-5xl text-yellow-300/90"></i>
        <div>
          <h1 class="text-4xl font-orbitron tracking-wide inline-block
           bg-gradient-to-r from-yellow-600 via-yellow-500 to-yellow-400
           bg-clip-text text-transparent">
  Editar Usuário
</h1>

          <p class="text-yellow-300/70 mt-2 font-poppins tracking-tight font-bold">Atualize os dados deste usuário</p>
          {{-- Badge do tipo (T-ID2) --}}
          <div class="mt-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs md:text-[13px] badge-gold">
            <i class="ph ph-shield-star text-[14px] opacity-80"></i>
            {{ ucfirst($usuario->tipo) }}
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Alertas de validação --}}
  @if ($errors->any())
    <div class="fade-in-up rounded-xl border border-red-500/30 bg-red-500/10 text-red-200 px-4 py-3">
      <div class="font-semibold mb-1">Corrija os campos abaixo:</div>
      <ul class="list-disc list-inside text-sm">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Container do formulário (glass + glow) --}}
  <div class="fade-in-up rounded-2xl bg-[#1a1410]/60 backdrop-blur-md border border-yellow-500/20 shadow-[0_0_16px_rgba(212,175,55,0.22)]">
    <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST" class="p-6 md:p-8 space-y-8">
      @csrf
      @method('PUT')

      {{-- Seção: Dados Pessoais (2 colunas) --}}
      <div>
        <h3 class="micro-heading text-sm font-semibold text-yellow-300/90 mb-4">Dados pessoais</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          {{-- Nome --}}
          <div>
            <label for="nome" class="block text-sm font-semibold text-yellow-200 mb-1.5">Nome</label>
            <div class="relative">
              <input id="nome" name="nome" type="text" value="{{ old('nome', $usuario->nome) }}"
                     class="w-full rounded-xl bg-[#241b16]/70 border border-yellow-500/30 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-500/30
                            text-[#f5e6d3] placeholder-yellow-300/40 px-4 py-2.5 transition-all"
                     placeholder="Ex.: João Silva" required>
              <i class="ph ph-identification-card absolute right-3 top-1/2 -translate-y-1/2 text-yellow-300/70"></i>
            </div>
          </div>

          {{-- Email --}}
          <div>
            <label for="email" class="block text-sm font-semibold text-yellow-200 mb-1.5">E-mail</label>
            <div class="relative">
              <input id="email" name="email" type="email" value="{{ old('email', $usuario->email) }}"
                     class="w-full rounded-xl bg-[#241b16]/70 border border-yellow-500/30 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-500/30
                            text-[#f5e6d3] placeholder-yellow-300/40 px-4 py-2.5 transition-all"
                     placeholder="email@exemplo.com" required>
              <i class="ph ph-at absolute right-3 top-1/2 -translate-y-1/2 text-yellow-300/70"></i>
            </div>
          </div>

          {{-- Telefone (opcional) --}}
          <div class="md:col-span-2">
            <label for="telefone" class="block text-sm font-semibold text-yellow-200 mb-1.5">Telefone (opcional)</label>
            <div class="relative">
              <input id="telefone" name="telefone" type="text" value="{{ old('telefone', $usuario->telefone) }}"
                     class="w-full rounded-xl bg-[#241b16]/70 border border-yellow-500/30 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-500/30
                            text-[#f5e6d3] placeholder-yellow-300/40 px-4 py-2.5 transition-all"
                     placeholder="(11) 99999-9999">
              <i class="ph ph-phone absolute right-3 top-1/2 -translate-y-1/2 text-yellow-300/70"></i>
            </div>
          </div>
        </div>
      </div>

      {{-- Seção: Permissões & Vínculos (2 colunas) --}}
      <div>
        <h3 class="micro-heading text-sm font-semibold text-yellow-300/90 mb-4">Permissões & Vínculos</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          {{-- Tipo --}}
<div>
  <label for="tipo" class="block text-sm font-semibold text-yellow-200 mb-1.5">Tipo de usuário</label>
  <div class="relative">
    <select id="tipo" name="tipo"
            class="w-full appearance-none rounded-xl bg-[#241b16]/70 border border-yellow-500/30 text-[#f5e6d3]
                   px-4 py-2.5 pr-10 focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-500/30 transition-all"
            required>

      {{-- SOMENTE ADMIN PODE VER E EDITAR ADMIN --}}
      @if($usuarioLogado->tipo === 'admin')
        <option value="admin"    @selected(old('tipo', $usuario->tipo) === 'admin')>Admin</option>
      @endif

      <option value="barbeiro" @selected(old('tipo', $usuario->tipo) === 'barbeiro')>Barbeiro</option>
      <option value="cliente"  @selected(old('tipo', $usuario->tipo) === 'cliente')>Cliente</option>
    </select>

    <i class="ph ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-yellow-300/70 pointer-events-none"></i>
  </div>
</div>


          {{-- Barbearia (somente admin enxerga todas) --}}
          <div>
            <label for="barbearia_id" class="block text-sm font-semibold text-yellow-200 mb-1.5">Barbearia</label>
            <div class="relative">
              <select id="barbearia_id" name="barbearia_id"
                      class="w-full appearance-none rounded-xl bg-[#241b16]/70 border border-yellow-500/30 text-[#f5e6d3]
                             px-4 py-2.5 pr-10 focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-500/30 transition-all"
                      @if(($usuarioLogado->tipo ?? null) !== 'admin') disabled @endif
              >
                @if(($usuarioLogado->tipo ?? null) === 'admin')
                  @foreach($barbearias as $b)
                    <option value="{{ $b->id }}" @selected(old('barbearia_id', $usuario->barbearia_id) == $b->id)>{{ $b->nome }}</option>
                  @endforeach
                @else
                  @if(isset($usuarioLogado->barbearia))
                    <option value="{{ $usuarioLogado->barbearia->id }}" selected>{{ $usuarioLogado->barbearia->nome }}</option>
                  @else
                    <option value="" selected>-</option>
                  @endif
                @endif
              </select>
              <i class="ph ph-storefront absolute right-9 top-1/2 -translate-y-1/2 text-yellow-300/70 pointer-events-none"></i>
              @if(($usuarioLogado->tipo ?? null) !== 'admin')
                <input type="hidden" name="barbearia_id" value="{{ old('barbearia_id', optional($usuarioLogado->barbearia)->id) }}">
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- Seção: Acesso (toggle de senha PW-EDIT2) --}}
      <div>
        <h3 class="micro-heading text-sm font-semibold text-yellow-300/90 mb-4">Acesso</h3>

        <button type="button"
                class="group inline-flex items-center gap-2 rounded-full px-4 py-2
                       bg-white/5 text-yellow-200 border border-white/10 hover:bg-white/10 transition-all mb-3"
                id="btnTogglePasswordFields">
          <i class="ph ph-key text-[18px]"></i>
          <span>Alterar senha</span>
        </button>

        {{-- Wrapper revelável (REVEAL3) --}}
        <div class="reveal-wrap" id="passwordReveal" data-open="false">
          <div class="reveal-inner">
            <div class="reveal-glow rounded-2xl p-4 border border-yellow-500/20 bg-[#1a1410]/55">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Nova senha --}}
<div>
  <label for="senha" class="block text-sm font-semibold text-yellow-200 mb-1.5">Nova senha</label>
  <div class="relative">
    <input id="senha" name="senha" type="password"
           class="w-full rounded-xl bg-[#241b16]/70 border border-yellow-500/30 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-500/30
                  text-[#f5e6d3] placeholder-yellow-300/40 px-4 py-2.5 pr-10 transition-all">
    <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 rounded-lg hover:bg-white/5" data-toggle-password="#senha">
      <i class="ph ph-eye text-yellow-300/80"></i>
    </button>
  </div>
</div>

{{-- Confirmar senha --}}
<div>
  <label for="senha_confirmation" class="block text-sm font-semibold text-yellow-200 mb-1.5">Confirmar nova senha</label>
  <div class="relative">
    <input id="senha_confirmation" name="senha_confirmation" type="password"
           class="w-full rounded-xl bg-[#241b16]/70 border border-yellow-500/30 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-500/30
                  text-[#f5e6d3] placeholder-yellow-300/40 px-4 py-2.5 pr-10 transition-all">
    <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 rounded-lg hover:bg-white/5" data-toggle-password="#senha_confirmation">
      <i class="ph ph-eye text-yellow-300/80"></i>
    </button>
  </div>
</div>

                </div>
              </div>

              <p class="text-xs text-yellow-300/70 mt-3">Deixe os campos vazios caso não deseje alterar a senha.</p>
            </div>
          </div>
        </div>
      </div>

      {{-- Ações --}}
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-2">
        <a href="{{ route('usuarios.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-full px-4 py-2.5
                  bg-white/5 text-yellow-200 border border-white/10 hover:bg-white/10 transition-all">
          <i class="ph ph-arrow-left"></i>
          Voltar
        </a>

        <button type="submit"
                class="group inline-flex items-center justify-center gap-2 rounded-full px-5 py-2.5
                       bg-gradient-to-r from-yellow-500 to-yellow-400 text-[#1a1410] font-semibold
                       shadow-[0_0_12px_rgba(212,175,55,0.25)]
                       hover:shadow-[0_0_24px_rgba(255,255,255,0.45)]
                       transition-all">
          <i class="ph ph-floppy-disk-back text-lg translate-y-0 group-hover:translate-y-[-1px] transition-transform"></i>
          Salvar Usuário
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle de exibição dos campos de senha (PW-EDIT2 + REVEAL3)
const btnToggle = document.getElementById('btnTogglePasswordFields');
const reveal = document.getElementById('passwordReveal');
if (btnToggle && reveal) {
  btnToggle.addEventListener('click', () => {
    const open = reveal.getAttribute('data-open') === 'true';
    reveal.setAttribute('data-open', open ? 'false' : 'true');
  });
}

// Toggle de senha (PW2)
document.querySelectorAll('[data-toggle-password]').forEach(btn => {
  btn.addEventListener('click', () => {
    const input = document.querySelector(btn.getAttribute('data-toggle-password'));
    if (!input) return;
    const showing = input.getAttribute('type') === 'text';
    input.setAttribute('type', showing ? 'password' : 'text');
    const icon = btn.querySelector('i');
    icon.classList.toggle('ph-eye');
    icon.classList.toggle('ph-eye-slash');
  });
});
</script>
@endpush
