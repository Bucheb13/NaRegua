<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Barbearia;
use App\Models\Usuario;
use App\Models\Servico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AgendamentoController extends Controller
{
    public function index(Request $request)
    {
        $usuario = Auth::user();
    
        // Barbearias
        if ($usuario->tipo === 'admin') {
            $barbearias = Barbearia::all();
            $barbeariaSelecionada = $request->query('barbearia_id')
                ? Barbearia::find($request->query('barbearia_id'))
                : $barbearias->first();
        } else {
            $barbearias = collect([$usuario->barbearia]);
            $barbeariaSelecionada = $usuario->barbearia;
        }
    
        // Barbeiros
        if ($usuario->tipo === 'admin' || $usuario->tipo === 'barbeiro') {
            $barbeiros = Usuario::where('tipo', 'barbeiro')
                ->when($barbeariaSelecionada, fn($q) => $q->where('barbearia_id', $barbeariaSelecionada->id))
                ->get();
    
            $barbeiroSelecionado = $request->query('barbeiro_id')
                ? Usuario::find($request->query('barbeiro_id'))
                : $barbeiros->first();
        } else {
            $barbeiros = collect();
            $barbeiroSelecionado = null;
        }
    
        $dataSelecionada = $request->query('data') ?? now()->format('Y-m-d');
    
        // Agendamentos existentes
        $agendamentos = Agendamento::with(['cliente', 'servico'])
            ->when($barbeariaSelecionada, fn($q) => $q->where('barbearia_id', $barbeariaSelecionada->id))
            ->when($barbeiroSelecionado, fn($q) => $q->where('barbeiro_id', $barbeiroSelecionado->id))
            ->whereDate('data_hora', $dataSelecionada)
            ->get();
    
// Horários disponíveis (com bloqueio completo do intervalo do serviço)
$horariosDisponiveis = [];
if ($barbeiroSelecionado) {
    $inicio = Carbon::parse($dataSelecionada . ' 09:00');
    $fim    = Carbon::parse($dataSelecionada . ' 18:00');

    for ($hora = $inicio->copy(); $hora->lt($fim); $hora->addMinutes(5)) {

        // Não mostrar horários passados se for no dia atual
        if ($dataSelecionada === now()->format('Y-m-d') && $hora->isPast()) {
            continue;
        }

        $ocupado = false;
        $motivo  = null;

        foreach ($agendamentos as $ag) {

            $inicioAg = Carbon::parse($ag->data_hora);
            $fimAg    = (clone $inicioAg)->addMinutes($ag->servico->duracao_minutos);

            // Verifica se este horário está dentro do intervalo
            if ($hora->between($inicioAg, $fimAg->copy()->subMinute())) {
                $ocupado = true;
                $motivo  = $ag->cliente->nome
                         . ' — ' . $ag->servico->nome
                         . ' (' . $ag->servico->duracao_minutos . ' min)';
                         $ocupado = true;
                         $status  = $ag->status;

                break;
            }
        }

        // AGORA dentro do for ✅
        $horariosDisponiveis[] = [
            'hora'     => $hora->format('H:i'),
            'dataHora' => $hora->format('Y-m-d H:i:00'),
            'ocupado'  => $ocupado,
            'motivo'   => $motivo,
            'status'   => $status,
        ];
    }
}

        return view('pages.agendamentos.index', compact(
            'usuario', 'barbearias', 'barbeariaSelecionada',
            'barbeiros', 'barbeiroSelecionado',
            'dataSelecionada', 'agendamentos', 'horariosDisponiveis'
        ));
    }


    public function create(Request $request)
    {
        $usuario = Auth::user();

        $barbearias = $usuario->tipo === 'admin' ? Barbearia::all() : collect([$usuario->barbearia]);
        $clientes = Usuario::where('tipo', 'cliente')
            ->when($usuario->tipo !== 'admin', fn($q) => $q->where('barbearia_id', $usuario->barbearia_id))
            ->get();
        $barbeiros = Usuario::where('tipo', 'barbeiro')
            ->when($usuario->tipo !== 'admin', fn($q) => $q->where('barbearia_id', $usuario->barbearia_id))
            ->get();
        $servicos = Servico::all();

        $barbeiro_id = $request->query('barbeiro_id');
$barbeiro = Usuario::find($barbeiro_id);
$barbearia_id = $request->query('barbearia_id') ?? $barbeiro->barbearia_id ?? null;
$data_hora = $request->query('data_hora');


        return view('pages.agendamentos.form', compact(
            'usuario', 'barbearias', 'clientes', 'barbeiros', 'servicos',
            'barbearia_id', 'barbeiro_id', 'data_hora'
        ));
    }

    public function store(Request $request)
    {
        $usuario = Auth::user();
    
        // Validação
        $request->validate([
            'cliente_id'    => 'required|exists:usuarios,id',
            'barbeiro_id'   => 'required|exists:usuarios,id',
            'barbearia_id'  => 'required|exists:barbearias,id',
            'servico_id'    => 'required|exists:servicos,id',
            'data_hora'     => 'required|date',
        ]);
    
        $servico = Servico::findOrFail($request->servico_id);
    
        $inicio = Carbon::parse($request->data_hora);
        $fim = (clone $inicio)->addMinutes($servico->duracao_minutos);
    
        // CONFLITO (agora com JOIN real em servicos)
        $conflito = Agendamento::query()
            ->join('servicos', 'servicos.id', '=', 'agendamentos.servico_id')
            ->where('agendamentos.barbeiro_id', $request->barbeiro_id)
            ->where('agendamentos.status', 'agendado')
            ->where(function ($query) use ($inicio, $fim) {
                $query->where('agendamentos.data_hora', '<', $fim)
                      ->whereRaw("agendamentos.data_hora + (servicos.duracao_minutos * INTERVAL '1 minute') > ?", [$inicio]);
            })
            ->exists();
    
        if ($conflito) {
            return back()->withErrors([
                'data_hora' => 'Este barbeiro já possui um atendimento nesta faixa de horário.'
            ])->withInput();
        }
    
        // Criar o agendamento
        Agendamento::create([
            'cliente_id'   => $request->cliente_id,
            'barbeiro_id'  => $request->barbeiro_id,
            'barbearia_id' => $request->barbearia_id,
            'servico_id'   => $request->servico_id,
            'data_hora'    => $inicio,
            'status'       => 'agendado',
        ]);
    
        return redirect()->route('agendamentos.index')->with('success', 'Agendamento criado com sucesso!');
    }
    
    /**
     * Editar agendamento
     */
    public function edit(Agendamento $agendamento)
    {
        $usuario = Auth::user();

        if (($usuario->tipo === 'barbeiro' && $agendamento->barbearia_id !== $usuario->barbearia_id) ||
            ($usuario->tipo === 'cliente' && $agendamento->cliente_id !== $usuario->id)) {
            abort(403, 'Acesso negado');
        }

        $barbearias = $usuario->tipo === 'admin'
            ? Barbearia::all()
            : collect([$usuario->barbearia]);

        $clientes = Usuario::where('tipo', 'cliente')
            ->when($usuario->tipo !== 'admin', fn($q) => $q->where('barbearia_id', $usuario->barbearia_id))
            ->get();

        $barbeiros = Usuario::where('tipo', 'barbeiro')
            ->when($usuario->tipo !== 'admin', fn($q) => $q->where('barbearia_id', $usuario->barbearia_id))
            ->get();

        $servicos = Servico::all();

        return view('pages.agendamentos.form', compact(
            'agendamento', 'usuario', 'barbearias', 'clientes', 'barbeiros', 'servicos'
        ));
    }

    /**
     * Atualizar agendamento
     */
    public function update(Request $request, Agendamento $agendamento)
    {
        $usuario = Auth::user();

        if (($usuario->tipo === 'barbeiro' && $agendamento->barbearia_id !== $usuario->barbearia_id) ||
            ($usuario->tipo === 'cliente' && $agendamento->cliente_id !== $usuario->id)) {
            abort(403, 'Acesso negado');
        }

        $request->validate([
            'cliente_id' => 'required|exists:usuarios,id',
            'barbeiro_id' => 'required|exists:usuarios,id',
            'barbearia_id' => 'required|exists:barbearias,id',
            'servico_id' => 'required|exists:servicos,id',
            'data_hora' => 'required|date',
            'status' => 'required|string|in:agendado,concluido,cancelado',
        ]);

        $barbeariaId = $usuario->tipo === 'admin' ? $request->barbearia_id : $usuario->barbearia_id;

        $agendamento->update([
            'cliente_id' => $request->cliente_id,
            'barbeiro_id' => $request->barbeiro_id,
            'barbearia_id' => $barbeariaId,
            'servico_id' => $request->servico_id,
            'data_hora' => $request->data_hora,
            'status' => $request->status,
        ]);

        return redirect()->route('agendamentos.index')->with('success', 'Agendamento atualizado com sucesso!');
    }
    /**
     * Atualizar apenas o status do agendamento
     */
    public function atualizarStatus(Request $request, Agendamento $agendamento)
    {
        $usuario = Auth::user();

        if (($usuario->tipo === 'barbeiro' && $agendamento->barbearia_id !== $usuario->barbearia_id) ||
            ($usuario->tipo === 'cliente')) {
            abort(403, 'Acesso negado');
        }

        $request->validate([
            'status' => 'required|string|in:agendado,concluido,cancelado',
        ]);

        $agendamento->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Status atualizado com sucesso!');
    }
    /**
     * Confirmar agendamento
     */
    public function confirmar($id)
    {
        $usuario = Auth::user();
        $agendamento = Agendamento::findOrFail($id);

        // Segurança: barbeiro só confirma se for da própria barbearia
        if ($usuario->tipo === 'barbeiro' && $agendamento->barbearia_id !== $usuario->barbearia_id) {
            abort(403, 'Acesso negado');
        }

        // Atualiza status
        $agendamento->update(['status' => 'concluido']);

        return redirect()->back()->with('success', 'Agendamento confirmado com sucesso!');
    }

    /**
     * Cancelar agendamento
     */
    public function cancelar($id)
    {
        $usuario = Auth::user();
        $agendamento = Agendamento::findOrFail($id);

        if ($usuario->tipo === 'barbeiro' && $agendamento->barbearia_id !== $usuario->barbearia_id) {
            abort(403, 'Acesso negado');
        }

        $agendamento->update(['status' => 'cancelado']);

        return redirect()->back()->with('success', 'Agendamento cancelado!');
    }


    /**
     * Deletar agendamento
     */

    public function destroy(Agendamento $agendamento)
    {
        $usuario = Auth::user();

        if (($usuario->tipo === 'barbeiro' && $agendamento->barbearia_id !== $usuario->barbearia_id) ||
            ($usuario->tipo === 'cliente' && $agendamento->cliente_id !== $usuario->id)) {
            abort(403, 'Acesso negado');
        }

        $agendamento->delete();

        return redirect()->route('agendamentos.index')->with('success', 'Agendamento deletado com sucesso!');
    }
}
