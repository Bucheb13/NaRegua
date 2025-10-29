<?php

namespace App\Http\Controllers;

use App\Models\Barbearia;
use App\Models\Usuario;
use App\Models\Venda;
use App\Models\Agendamento;
use App\Models\Servico;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index(Request $request)
{
    $usuario = Auth::user();
    if ($usuario->tipo === 'cliente') {
        abort(403, 'Acesso negado');
    }

    // ===== Filtro de Barbearia
    if ($usuario->tipo === 'admin') {
        $barbearias = Barbearia::all();
        $barbeariaId = $request->query('barbearia_id');
        $barbeariaSelecionada = $barbeariaId ? Barbearia::find($barbeariaId) : null;
    } else {
        $barbearias = collect([$usuario->barbearia]);
        $barbeariaSelecionada = $usuario->barbearia;
    }

    // ===== Janelas de tempo (rolling 12 meses)
    $end   = now()->endOfMonth();
    $start = (clone $end)->subMonths(11)->startOfMonth();

    // ===== Labels de meses (rolling), ex.: nov, dez, jan...
    $periodo = collect(range(0, 11))->map(fn ($i) => (clone $start)->addMonths($i));
    $meses   = $periodo->map(fn ($d) => $d->locale('pt_BR')->isoFormat('MMM'));
    // chave yyyy-mm para indexar plucks
    $ymKeys  = $periodo->map(fn ($d) => $d->format('Y-m'));

    // ===== Bases sem JOIN (evita duplicidade)
    $baseVendas = $barbeariaSelecionada
        ? $barbeariaSelecionada->vendas()->newQuery()
        : Venda::query();

    $baseAg = $barbeariaSelecionada
        ? $barbeariaSelecionada->agendamentos()->newQuery()
        : Agendamento::query();

    // ===== KPIs (mês atual) – receita
    $inicioMes = now()->startOfMonth();
    $fimMes    = now()->endOfMonth();

    $totalProdutos = (clone $baseVendas)
        ->whereBetween('created_at', [$inicioMes, $fimMes])
        ->sum('valor_total');

    $totalServicos = Agendamento::query()
        ->when($barbeariaSelecionada, fn ($q) => $q->where('agendamentos.barbearia_id', $barbeariaSelecionada->id))
        ->where('status', 'concluido')
        ->whereBetween('data_hora', [$inicioMes, $fimMes])
        ->join('servicos', 'agendamentos.servico_id', '=', 'servicos.id')
        ->sum('servicos.preco');

    $totalVendas = $totalProdutos + $totalServicos;

    // ===== Outros KPIs gerais
    $totalAgendamentos = (clone $baseAg)->count();
    $agendamentosHoje  = (clone $baseAg)
    ->whereBetween('data_hora', [now()->startOfDay(), now()->endOfDay()])
    ->count();


    $totalUsuarios = $barbeariaSelecionada
        ? $barbeariaSelecionada->usuarios()->count()
        : Usuario::count();

    $taxaOcupacao = $totalAgendamentos > 0
        ? round(($agendamentosHoje / $totalAgendamentos) * 100)
        : 0;

   // ===== Próximos agendamentos (futuros)
   $proximosAgendamentos = (clone $baseAg)
   ->with(['cliente', 'servico'])
   ->whereIn('status', ['agendado', 'pendente', 'concluido', 'cancelado'])
   ->where('data_hora', '>=', now()->subHour())
   ->orderBy('data_hora', 'asc')
   ->limit(10)
   ->get();



    // ==========================================================
    // ================== GRÁFICOS (ROLLING 12) ==================
    // ==========================================================

    // ---- Produtos por mês (vendas.valor_total)
    $produtosPorMes = (clone $baseVendas)
        ->whereBetween('created_at', [$start, $end])
        ->selectRaw("to_char(date_trunc('month', created_at), 'YYYY-MM') as ym, COALESCE(SUM(valor_total),0) as total")
        ->groupBy('ym')
        ->pluck('total', 'ym'); // ['2025-01' => 1234.56, ...]

    // ---- Serviços por mês (soma do servicos.preco de agendamentos concluídos)
    $servicosPorMes = Agendamento::query()
        ->when($barbeariaSelecionada, fn ($q) => $q->where('agendamentos.barbearia_id', $barbeariaSelecionada->id))
        ->where('status', 'concluido')
        ->whereBetween('data_hora', [$start, $end])
        ->join('servicos', 'agendamentos.servico_id', '=', 'servicos.id')
        ->selectRaw("to_char(date_trunc('month', data_hora), 'YYYY-MM') as ym, COALESCE(SUM(servicos.preco),0) as total")
        ->groupBy('ym')
        ->pluck('total', 'ym');

    // ---- Agendamentos por status (empilhado)
    // Considera status em ['pendente','agendado','concluido','cancelado']
    $agBruto = Agendamento::query()
        ->when($barbeariaSelecionada, fn ($q) => $q->where('agendamentos.barbearia_id', $barbeariaSelecionada->id))
        ->whereBetween('data_hora', [$start, $end])
        ->whereIn('status', ['pendente','agendado','concluido','cancelado'])
        ->selectRaw("to_char(date_trunc('month', data_hora), 'YYYY-MM') as ym, status, COUNT(*) as total")
        ->groupBy('ym', 'status')
        ->get();

    // Normaliza 'agendado' -> 'pendente'
    $mapPendentes = [];
    $mapConcluidos = [];
    $mapCancelados = [];
    foreach ($ymKeys as $ym) {
        $mapPendentes[$ym] = 0;
        $mapConcluidos[$ym] = 0;
        $mapCancelados[$ym] = 0;
    }
    foreach ($agBruto as $row) {
        $ym = $row->ym;
        $st = $row->status;
        $tot = (int)$row->total;
        if (!array_key_exists($ym, $mapPendentes)) continue; // fora da janela (só por segurança)

        if ($st === 'concluido') {
            $mapConcluidos[$ym] += $tot;
        } elseif ($st === 'cancelado') {
            $mapCancelados[$ym] += $tot;
        } else { // 'pendente' ou 'agendado'
            $mapPendentes[$ym] += $tot;
        }
    }

    // ---- Monta arrays finais alinhados aos 12 meses
    $arrProdutos = [];
    $arrServicos = [];
    $arrPend     = [];
    $arrConc     = [];
    $arrCanc     = [];
    $arrTotalAg  = [];

    foreach ($ymKeys as $ym) {
        $p = (float)($produtosPorMes[$ym] ?? 0);
        $s = (float)($servicosPorMes[$ym] ?? 0);

        $pend = (int)($mapPendentes[$ym] ?? 0);
        $conc = (int)($mapConcluidos[$ym] ?? 0);
        $canc = (int)($mapCancelados[$ym] ?? 0);

        $arrProdutos[] = $p;
        $arrServicos[] = $s;

        $arrPend[] = $pend;
        $arrConc[] = $conc;
        $arrCanc[] = $canc;
        $arrTotalAg[] = $pend + $conc + $canc;
    }

    // ---- Estruturas esperadas pela VIEW
    $vendasMensais = [
        'meses'    => $meses,
        'produtos' => $arrProdutos,
        'servicos' => $arrServicos, // somente concluídos
    ];

    $agendamentosMensais = [
        'meses'      => $meses,
        'pendentes'  => $arrPend,
        'concluidos' => $arrConc,
        'cancelados' => $arrCanc,
        'total'      => $arrTotalAg,
    ];

    return view('pages.dashboard.index', compact(
        'usuario',
        'barbearias',
        'barbeariaSelecionada',
        'totalVendas',
        'totalProdutos',
        'totalServicos',
        'totalAgendamentos',
        'agendamentosHoje',
        'taxaOcupacao',
        'totalUsuarios',
        'vendasMensais',
        'agendamentosMensais',
        'proximosAgendamentos'
    ));
}

    public function concluir(Agendamento $agendamento)
    {
        $agendamento->update(['status' => 'concluido']);
        return back()->with('success', 'Agendamento marcado como concluído!');
    }
}
