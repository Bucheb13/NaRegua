@foreach ($agendamentos as $ag)
    @php
        $status = $ag->status ?? 'agendado';
        $statusMap = [
            'agendado' => ['bg' => 'bg-yellow-500/20', 'text' => 'text-yellow-300', 'border' => 'border-yellow-500/30', 'label' => 'Agendado'],
            'concluido' => ['bg' => 'bg-green-500/20', 'text' => 'text-green-300', 'border' => 'border-green-500/30', 'label' => 'Concluído'],
            'cancelado' => ['bg' => 'bg-red-500/20', 'text' => 'text-red-300', 'border' => 'border-red-500/30', 'label' => 'Cancelado'],
        ];
        $st = $statusMap[$status] ?? $statusMap['agendado'];
        $valor = $ag->valor ?? $ag->servico->valor ?? null;
    @endphp

    <div class="rounded-xl border {{ $st['border'] }} {{ $st['bg'] }} p-4">
        <div class="flex items-start justify-between">
            <div>
                <div class="font-semibold text-[#f5e6d3]">{{ $ag->cliente->nome ?? '—' }}</div>
                <div class="text-sm text-yellow-200/80">{{ $ag->servico->nome ?? '—' }}</div>
            </div>
            <span class="px-2 py-1 text-[11px] rounded-lg border {{ $st['border'] }} {{ $st['text'] }}">
                {{ $st['label'] }}
            </span>
        </div>

        <div class="mt-3 grid grid-cols-2 gap-2 text-[13px] text-yellow-200/80">
            <div>
                <div class="opacity-70">Data</div>
                <div class="font-medium text-[#f5e6d3]">{{ optional($ag->data_hora)->format('d/m/Y') }}</div>
            </div>
            <div>
                <div class="opacity-70">Hora</div>
                <div class="font-medium text-[#f5e6d3]">{{ optional($ag->data_hora)->format('H:i') }}</div>
            </div>
            <div>
                <div class="opacity-70">Valor</div>
                <div class="font-medium text-[#f5e6d3]">{{ $valor ? 'R$ ' . number_format($valor, 2, ',', '.') : '—' }}</div>
            </div>
            <div>
                <div class="opacity-70">Barbeiro</div>
                <div class="font-medium text-[#f5e6d3]">{{ $ag->barbeiro->nome ?? '—' }}</div>
            </div>
        </div>
    </div>
@endforeach
