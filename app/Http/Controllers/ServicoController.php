<?php

namespace App\Http\Controllers;

use App\Models\Servico;
use App\Models\Barbearia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServicoController extends Controller
{
    /**
     * Lista todos os serviços, filtrando por barbearia se não for admin.
     */
    public function index(Request $request)
{
    $usuario = Auth::user();

    // Admin vê todas / outros apenas sua barbearia
    $barbearias = $usuario->tipo === 'admin'
        ? Barbearia::all()
        : collect([$usuario->barbearia]);

    // ✅ Seleciona um OBJETO Barbearia e NÃO um ID
    if ($usuario->tipo === 'admin') {
        if ($request->barbearia_id) {
            $barbeariaSelecionada = Barbearia::find($request->barbearia_id);
        } else {
            $barbeariaSelecionada = $barbearias->first();
        }
    } else {
        $barbeariaSelecionada = $usuario->barbearia; // objeto direto
    }

    // Lista serviços referentes à barbearia selecionada
    $servicos = Servico::with('barbearia')
        ->where('barbearia_id', $barbeariaSelecionada->id)
        ->get();

    return view('pages.servicos.index', compact(
        'servicos',
        'usuario',
        'barbearias',
        'barbeariaSelecionada'
    ));
}

    

    /**
     * Formulário de criação de serviço
     */
    public function create()
    {
        $usuario = Auth::user();

        if (!in_array($usuario->tipo, ['admin', 'barbeiro'])) {
            abort(403, 'Acesso negado.');
        }

        $barbearias = $usuario->tipo === 'admin'
            ? Barbearia::all()
            : collect([$usuario->barbearia]);

        return view('pages.servicos.create', compact('usuario', 'barbearias'));
    }

    /**
     * Salva um novo serviço
     */
    public function store(Request $request)
    {
        $usuario = Auth::user();
        if (!in_array($usuario->tipo, ['admin', 'barbeiro'])) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
            'preco' => 'required|numeric|min:0',
            'duracao_minutos' => 'required|integer|min:1',
            'barbearia_id' => $usuario->tipo === 'admin' ? 'required|exists:barbearias,id' : '', // Admin precisa escolher
        ]);

        Servico::create([
            'barbearia_id' => $usuario->tipo === 'admin' ? $request->barbearia_id : $usuario->barbearia_id,
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'preco' => $request->preco,
            'duracao_minutos' => $request->duracao_minutos,
        ]);

        return redirect()->route('servicos.index')->with('success', 'Serviço criado com sucesso!');
    }

    /**
     * Formulário de edição
     */
    public function edit(Servico $servico)
    {
        $usuario = Auth::user();
        if ($usuario->tipo === 'cliente' || ($usuario->tipo === 'barbeiro' && $servico->barbearia_id !== $usuario->barbearia_id)) {
            abort(403, 'Acesso negado.');
        }

        $barbearias = $usuario->tipo === 'admin'
            ? Barbearia::all()
            : collect([$usuario->barbearia]);

        return view('pages.servicos.edit', compact('servico', 'usuario', 'barbearias'));
    }

    /**
     * Atualiza o serviço
     */
    public function update(Request $request, Servico $servico)
    {
        $usuario = Auth::user();
        if ($usuario->tipo === 'cliente' || ($usuario->tipo === 'barbeiro' && $servico->barbearia_id !== $usuario->barbearia_id)) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
            'preco' => 'required|numeric|min:0',
            'duracao_minutos' => 'required|integer|min:1',
            'barbearia_id' => $usuario->tipo === 'admin' ? 'required|exists:barbearias,id' : '',
        ]);

        $servico->update([
            'barbearia_id' => $usuario->tipo === 'admin' ? $request->barbearia_id : $usuario->barbearia_id,
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'preco' => $request->preco,
            'duracao_minutos' => $request->duracao_minutos,
        ]);

        return redirect()->route('servicos.index')->with('success', 'Serviço atualizado com sucesso!');
    }

    /**
     * Exclui o serviço (apenas admin)
     */
    public function destroy(Servico $servico)
    {
        $usuario = Auth::user();
        if ($usuario->tipo !== 'admin') {
            abort(403, 'Acesso negado.');
        }

        $servico->delete();

        return redirect()->route('servicos.index')->with('success', 'Serviço excluído com sucesso!');
    }
}
