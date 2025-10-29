@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">

    {{-- Título da página --}}
    <h1 class="text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-yellow-300 via-yellow-400 to-yellow-200 tracking-tight flex items-center gap-3">
        <i class="ph ph-shopping-cart-simple text-yellow-300/90 text-4xl"></i>
        Vendas
    </h1>

    {{-- Mensagens --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-900/40 border border-emerald-400/30 text-emerald-200 px-4 py-3 rounded-2xl shadow-lg">
            <i class="ph ph-check-circle text-2xl"></i>
            <span>{{ session('success') }}</span>
        </div>
    @elseif(session('error'))
        <div class="flex items-center gap-3 bg-red-900/40 border border-red-400/30 text-red-200 px-4 py-3 rounded-2xl shadow-lg">
            <i class="ph ph-warning-circle text-2xl"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Seção de Produtos / Registro de Venda --}}
        <div class="bg-[#1a1410]/60 backdrop-blur-md border border-yellow-500/20 rounded-2xl p-6 shadow-lg">
            <h2 class="text-xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-yellow-300 via-yellow-400 to-yellow-200 mb-4 flex items-center gap-2">
                <i class="ph ph-package text-yellow-300/90 text-2xl"></i>
                Produtos Disponíveis
            </h2>

            <form action="{{ route('vendas.store') }}" method="POST" id="formVenda" class="space-y-4">
                @csrf

                {{-- Cliente --}}
                <div>
                    <label class="block text-sm font-semibold text-yellow-200/90 mb-2">Cliente</label>
                    <div class="relative">
                        <input
                            type="text"
                            id="cliente_nome"
                            placeholder="Digite o nome do cliente..."
                            autocomplete="off"
                            class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-3 py-2 text-yellow-100 placeholder:text-yellow-100/30 focus:outline-none focus:ring-1 focus:ring-yellow-400/40"
                        >
                        <input type="hidden" name="cliente_id" id="cliente_id">

                        {{-- Dropdown de autocomplete --}}
                        <ul id="resultado_clientes"
                            class="absolute z-20 mt-2 w-full bg-[#1a1410]/95 border border-yellow-500/20 rounded-lg shadow-xl hidden max-h-56 overflow-y-auto text-yellow-100">
                        </ul>
                    </div>

                    {{-- Botão para abrir modal --}}
                    <button
                        type="button"
                        id="btnNovoCliente"
                        class="mt-2 inline-flex items-center gap-2 text-sm text-yellow-300 hover:text-yellow-200 transition">
                        <i class="ph ph-user-plus text-base"></i>
                        Cadastrar novo cliente
                    </button>
                </div>

{{-- Produtos --}}
<div class="space-y-2 max-h-80 overflow-y-auto pr-1 custom-scroll">
    @foreach($produtos as $produto)
        <div class="flex items-center justify-between bg-[#241b16]/40 border border-yellow-500/10 rounded-lg px-3 py-3 hover:border-yellow-500/30 transition">
            <div class="space-y-1">
                <span class="font-semibold text-yellow-100">{{ $produto->nome }}</span>
                <p class="text-sm text-yellow-100/60">
                    R$ {{ number_format($produto->preco, 2, ',', '.') }}
                </p>

                @if($produto->quantidade_estoque == 0)
                    <p class="text-red-400 text-xs flex items-center gap-1">
                        <i class="ph ph-x-circle"></i> Sem estoque
                    </p>
                @elseif($produto->quantidade_estoque <= 5)
                    <p class="text-yellow-300 text-xs flex items-center gap-1">
                        <i class="ph ph-warning-circle"></i> Apenas {{ $produto->quantidade_estoque }} em estoque
                    </p>
                @else
                    <p class="text-emerald-300 text-xs flex items-center gap-1">
                        <i class="ph ph-check-circle"></i> {{ $produto->quantidade_estoque }} em estoque
                    </p>
                @endif
            </div>

            {{-- Campo de quantidade --}}
            <div class="flex items-center">
                {{-- envia 0 caso produto não possa ser escolhido --}}
                <input type="hidden" name="produtos[{{ $produto->id }}][quantidade]" value="0">

                @if($produto->quantidade_estoque > 0)
                    <input
                        type="number"
                        name="produtos[{{ $produto->id }}][quantidade]"
                        min="0"
                        max="{{ $produto->quantidade_estoque }}"
                        class="w-24 text-center bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-2 py-2 text-yellow-100 placeholder:text-yellow-100/30 focus:outline-none focus:ring-1 focus:ring-yellow-400/40 no-spin"
                        placeholder="0"
                    >
                @endif
            </div>
        </div>
    @endforeach
</div>


                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-yellow-600 via-yellow-500 to-yellow-400 hover:from-yellow-500 hover:via-yellow-400 hover:to-yellow-300 text-[#1a1410] px-6 py-2.5 rounded-xl font-bold shadow-lg hover:shadow-yellow-500/20 transition">
                        <i class="ph ph-currency-circle-dollar text-xl"></i>
                        Registrar Venda
                    </button>
                </div>
            </form>
        </div>

        {{-- Vendas Realizadas --}}
        <div class="bg-[#1a1410]/60 backdrop-blur-md border border-yellow-500/20 rounded-2xl p-6 shadow-lg">
            <h2 class="text-xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-yellow-300 via-yellow-400 to-yellow-200 mb-4 flex items-center gap-2">
                <i class="ph ph-currency-circle-dollar text-yellow-300/90 text-2xl"></i>
                Vendas Realizadas
            </h2>

            <div class="overflow-x-auto rounded-xl border border-yellow-500/10">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#1a1410]/80 border-b border-yellow-500/20">
                        <tr class="text-yellow-200/90">
                            <th class="p-3 text-left font-semibold">#</th>
                            <th class="p-3 text-left font-semibold">Cliente</th>
                            <th class="p-3 text-left font-semibold">Data</th>
                            <th class="p-3 text-left font-semibold">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-yellow-500/10">
                        @forelse($vendas as $venda)
                            <tr class="text-yellow-100 hover:bg-[#241b16]/50 transition">
                                <td class="p-3">{{ $venda->id }}</td>
                                <td class="p-3">{{ $venda->cliente->nome ?? 'N/A' }}</td>
                                <td class="p-3">{{ \Carbon\Carbon::parse($venda->data_venda)->format('d/m/Y H:i') }}</td>
                                <td class="p-3 font-semibold">
                                    <span class="inline-flex items-center gap-1 text-yellow-200">
                                        <i class="ph ph-currency-dollar-simple text-base"></i>
                                        R$ {{ number_format($venda->valor_total, 2, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center p-6 text-yellow-100/60">
                                    Nenhuma venda registrada ainda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- Modal fora do formVenda (importante: evita submissão do form de vendas) --}}
<div id="modalCliente" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
    <div class="bg-[#1a1410]/90 backdrop-blur-md border border-yellow-500/20 rounded-2xl p-6 w-[92%] max-w-md shadow-2xl relative">
        <div class="flex items-start justify-between mb-4">
            <h3 class="text-lg font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-yellow-300 via-yellow-400 to-yellow-200 flex items-center gap-2">
                <i class="ph ph-user-plus text-yellow-300/90 text-2xl"></i>
                Cadastrar Novo Cliente
            </h3>
            <button type="button" id="fecharModal"
                class="text-yellow-200/70 hover:text-yellow-200 transition">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <form id="formNovoCliente" onsubmit="return false;" class="space-y-3">
            @csrf
            <div id="erros_novo_cliente" class="text-red-300 text-sm"></div>

            <div>
                <label class="block text-sm font-medium text-yellow-200/90 mb-1">Nome</label>
                <input type="text" name="nome" required
                    class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-3 py-2 text-yellow-100 placeholder:text-yellow-100/30 focus:outline-none focus:ring-1 focus:ring-yellow-400/40">
            </div>
            <div>
                <label class="block text-sm font-medium text-yellow-200/90 mb-1">Email</label>
                <input type="email" name="email"
                    class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-3 py-2 text-yellow-100 placeholder:text-yellow-100/30 focus:outline-none focus:ring-1 focus:ring-yellow-400/40">
            </div>
            <div>
                <label class="block text-sm font-medium text-yellow-200/90 mb-1">Telefone</label>
                <input type="text" name="telefone"
                    class="w-full bg-[#241b16]/60 border border-yellow-500/20 rounded-lg px-3 py-2 text-yellow-100 placeholder:text-yellow-100/30 focus:outline-none focus:ring-1 focus:ring-yellow-400/40">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" id="fecharModal2"
                    class="px-4 py-2 rounded-xl border border-yellow-500/20 text-yellow-100 hover:bg-[#241b16]/60 transition">
                    Cancelar
                </button>
                <button type="button" id="btnSalvarCliente"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-yellow-600 via-yellow-500 to-yellow-400 hover:from-yellow-500 hover:via-yellow-400 hover:to-yellow-300 text-[#1a1410] px-5 py-2 rounded-xl font-semibold shadow-lg hover:shadow-yellow-500/20 transition">
                    <i class="ph ph-floppy-disk"></i>
                    Salvar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Scripts --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Elementos principais
    const inputCliente = document.getElementById('cliente_nome');
    const resultadoClientes = document.getElementById('resultado_clientes');
    const inputClienteId = document.getElementById('cliente_id');
    const btnNovoCliente = document.getElementById('btnNovoCliente');
    const modal = document.getElementById('modalCliente');
    const fecharModal = document.getElementById('fecharModal');
    const fecharModal2 = document.getElementById('fecharModal2');
    const formNovoCliente = document.getElementById('formNovoCliente');
    const btnSalvarCliente = document.getElementById('btnSalvarCliente');
    const errosContainer = document.getElementById('erros_novo_cliente');

    // helper: pega CSRF
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || document.querySelector('input[name="_token"]')?.value
            || '{{ csrf_token() }}';
    }

    // ----------------------
    // Autocomplete clientes (APENAS tipo=cliente)
    // ----------------------
    let debounceTimer;
    if (inputCliente) {
        inputCliente.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            const termo = inputCliente.value.trim();
            if (termo.length < 2) {
                resultadoClientes?.classList.add('hidden');
                return;
            }
            debounceTimer = setTimeout(() => buscarClientes(termo), 250);
        });
    }

    async function buscarClientes(termo) {
        try {
            const res = await fetch(`/usuarios/buscar?q=${encodeURIComponent(termo)}&tipo=cliente`, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            });
            if (!res.ok) throw new Error('Erro na busca: ' + res.status);
            const data = await res.json();

            if (!resultadoClientes) return;
            resultadoClientes.innerHTML = '';
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(cliente => {
                    const li = document.createElement('li');
                    li.innerHTML = `
                        <div class="px-3 py-2 flex items-center justify-between">
                            <span class="text-yellow-100">${cliente.nome}</span>
                            <span class="text-xs text-yellow-100/50">${cliente.telefone || 'sem telefone'}</span>
                        </div>`;
                    li.className = 'hover:bg-[#241b16]/60 cursor-pointer border-b border-yellow-500/10 last:border-none';
                    li.onclick = () => {
                        inputCliente.value = cliente.nome;
                        if (inputClienteId) inputClienteId.value = cliente.id;
                        resultadoClientes.classList.add('hidden');
                    };
                    resultadoClientes.appendChild(li);
                });
                resultadoClientes.classList.remove('hidden');
            } else {
                resultadoClientes.innerHTML = `<li class="px-3 py-2 text-yellow-100/60">Nenhum cliente encontrado</li>`;
                resultadoClientes.classList.remove('hidden');
            }
        } catch (err) {
            console.error("Erro no autocomplete:", err);
            if (resultadoClientes) {
                resultadoClientes.innerHTML = `<li class="px-3 py-2 text-red-300">Erro ao buscar clientes</li>`;
                resultadoClientes.classList.remove('hidden');
            }
        }
    }

    // fecha lista ao clicar fora
    document.addEventListener('click', (e) => {
        if (resultadoClientes && !resultadoClientes.contains(e.target) && e.target !== inputCliente) {
            resultadoClientes.classList.add('hidden');
        }
    });

    // ----------------------
    // Modal abrir/fechar
    // ----------------------
    function abrirModal() {
        if (!modal) return;
        errosContainer && (errosContainer.innerHTML = '');
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
    }
    function fecharModalFunc() {
        if (!modal) return;
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }

    btnNovoCliente && btnNovoCliente.addEventListener('click', abrirModal);
    fecharModal && fecharModal.addEventListener('click', fecharModalFunc);
    fecharModal2 && fecharModal2.addEventListener('click', fecharModalFunc);
    modal && modal.addEventListener('click', e => { if (e.target === modal) fecharModalFunc(); });

    // ----------------------
    // Envio do formulário de novo cliente (via fetch)
    // ----------------------
    async function safeParseError(res) {
        try {
            const json = await res.json();
            if (json && json.message) return json.message;
            if (json && json.errors) return JSON.stringify(json.errors);
            return JSON.stringify(json);
        } catch (e) {
            try { return await res.text(); } catch (_) { return null; }
        }
    }

    function displayValidationErrors(errors) {
        if (!errosContainer) {
            alert(Object.values(errors).flat().join('\n'));
            return;
        }
        errosContainer.innerHTML = '';
        const ul = document.createElement('ul');
        ul.className = 'list-disc pl-5 space-y-1';
        Object.keys(errors).forEach(field => {
            errors[field].forEach(msg => {
                const li = document.createElement('li');
                li.textContent = msg;
                ul.appendChild(li);
            });
        });
        errosContainer.appendChild(ul);
    }

    if (btnSalvarCliente && formNovoCliente) {
        btnSalvarCliente.addEventListener('click', async () => {
            btnSalvarCliente.disabled = true;
            errosContainer && (errosContainer.innerHTML = '');

            const formData = new FormData(formNovoCliente);
            formData.set('tipo', 'cliente');
            if (!formData.get('senha')) formData.set('senha', '123456'); // apenas temporário

            const csrfToken = getCsrfToken();

            try {
                const res = await fetch('/usuarios', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: formData
                });

                if (res.status === 419) {
                    throw new Error('Sessão expirada / token CSRF inválido. Refaça login e tente novamente.');
                }
                if (res.status === 403) {
                    const msg = await safeParseError(res);
                    throw new Error('Acesso negado: ' + (msg || '403'));
                }
                if (res.status === 422) {
                    const payload = await res.json();
                    const errors = payload.errors || {};
                    displayValidationErrors(errors);
                    throw new Error('Validação falhou');
                }
                if (!res.ok) {
                    const errText = await safeParseError(res);
                    throw new Error(errText || 'Erro ao salvar cliente');
                }

                const novoCliente = await res.json();
                // preenche o input do formulário de vendas
                if (inputCliente) inputCliente.value = novoCliente.nome;
                if (inputClienteId) inputClienteId.value = novoCliente.id;

                // Fecha modal e limpa
                fecharModalFunc();
                formNovoCliente.reset();
                if (resultadoClientes) {
                    resultadoClientes.innerHTML = '';
                    resultadoClientes.classList.add('hidden');
                }
            } catch (err) {
                if (err.message !== 'Validação falhou') {
                    alert(err.message || 'Erro ao cadastrar cliente');
                }
                console.error(err);
            } finally {
                btnSalvarCliente.disabled = false;
            }
        });
    }
});
</script>

{{-- Pequeno estilo opcional para scrollbar dos lists/containers (opcional) --}}
<style>
.custom-scroll::-webkit-scrollbar { width: 8px; }
.custom-scroll::-webkit-scrollbar-track { background: transparent; }
.custom-scroll::-webkit-scrollbar-thumb { background: rgba(207,174,112,0.25); border-radius: 9999px; }
.custom-scroll::-webkit-scrollbar-thumb:hover { background: rgba(207,174,112,0.45); }
/* remove spinner dos inputs type="number" */
.no-spin::-webkit-inner-spin-button,
.no-spin::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.no-spin {
    -moz-appearance: textfield;
}

</style>
@endsection
