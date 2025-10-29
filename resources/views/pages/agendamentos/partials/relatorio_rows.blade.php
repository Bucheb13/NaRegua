@foreach ($agendamentos as $ag)
    <tr class="border-b border-yellow-500/10 hover:bg-yellow-500/5 transition">
        <td class="py-3 pr-2">{{ $ag->cliente->nome ?? '—' }}</td>
        <td class="py-3 pr-2">{{ $ag->servico->nome ?? '—' }}</td>
        <td class="py-3 pr-2">{{ optional($ag->data_hora)->format('d/m/Y') }}</td>
        <td class="py-3 pr-2">{{ optional($ag->data_hora)->format('H:i') }}</td>
        <td class="py-3 pr-2 text-right">
            @php $valor = $ag->valor ?? $ag->servico->valor ?? null; @endphp
            {{ $valor ? 'R$ ' . number_format($valor, 2, ',', '.') : '—' }}
        </td>
        <td class="py-3">
            @php
                $status = $ag->status ?? 'agendado';
                $map = [
                    'agendado' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
                    'concluido' => 'bg-green-500/20 text-green-300 border-green-500/30',
                    'cancelado' => 'bg-red-500/20 text-red-300 border-red-500/30',
                ];
            @endphp
            <span class="px-2 py-1 text-xs rounded-lg border {{ $map[$status] ?? 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30' }}">
                {{ ucfirst($status) }}
            </span>
        </td>
    </tr>
@endforeach
