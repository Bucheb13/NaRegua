<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Barbearia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProdutoController extends Controller
{
    public function index(Request $request)
    {
        $usuario = Auth::user();

        // Admin pode filtrar por barbearia
        if ($usuario->tipo === 'admin') {
            $barbearias = Barbearia::all();
            $barbeariaSelecionada = $request->barbearia_id
                ? Barbearia::find($request->barbearia_id)
                : null;

            $produtos = Produto::with('barbearia')
                ->when($barbeariaSelecionada, function($query) use ($barbeariaSelecionada) {
                    $query->where('barbearia_id', $barbeariaSelecionada->id);
                })
                ->get();
        } else {
            // Barbeiro e cliente veem apenas produtos da própria barbearia
            $barbearias = collect([$usuario->barbearia]);
            $barbeariaSelecionada = $usuario->barbearia;

            $produtos = Produto::with('barbearia')
                ->where('barbearia_id', $usuario->barbearia_id)
                ->get();
        }

        return view('pages.produtos.index', compact(
            'produtos',
            'usuario',
            'barbearias',
            'barbeariaSelecionada'
        ));
    }

    public function create()
    {
        $usuario = Auth::user();
        $barbearias = $usuario->tipo === 'admin'
            ? Barbearia::all()
            : collect([$usuario->barbearia]);

        return view('pages.produtos.create', compact('usuario', 'barbearias'));
    }

    public function store(Request $request)
    {
        $usuario = Auth::user();

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'quantidade_estoque' => 'required|integer|min:0',
            'barbearia_id' => 'nullable|exists:barbearias,id',
        ]);

        $barbeariaId = $usuario->tipo === 'admin' ? $request->barbearia_id : $usuario->barbearia_id;

        Produto::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'preco' => $request->preco,
            'quantidade_estoque' => $request->quantidade_estoque,
            'barbearia_id' => $barbeariaId,
        ]);

        return redirect()->route('produtos.index')->with('success', 'Produto criado com sucesso!');
    }

    public function edit(Produto $produto)
    {
        $usuario = Auth::user();

        // Barbeiro/Cliente só podem editar produtos da própria barbearia
        if ($usuario->tipo !== 'admin' && $produto->barbearia_id !== $usuario->barbearia_id) {
            abort(403, 'Acesso negado');
        }

        $barbearias = $usuario->tipo === 'admin'
            ? Barbearia::all()
            : collect([$usuario->barbearia]);

        return view('pages.produtos.edit', compact('produto', 'usuario', 'barbearias'));
    }

    public function update(Request $request, Produto $produto)
    {
        $usuario = Auth::user();

        if ($usuario->tipo !== 'admin' && $produto->barbearia_id !== $usuario->barbearia_id) {
            abort(403, 'Acesso negado');
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'quantidade_estoque' => 'required|integer|min:0',
            'barbearia_id' => 'nullable|exists:barbearias,id',
        ]);

        $barbeariaId = $usuario->tipo === 'admin' ? $request->barbearia_id : $usuario->barbearia_id;

        $produto->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'preco' => $request->preco,
            'quantidade_estoque' => $request->quantidade_estoque,
            'barbearia_id' => $barbeariaId,
        ]);

        return redirect()->route('produtos.index')->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Produto $produto)
    {
        $usuario = Auth::user();

        if ($usuario->tipo === 'admin' || ($usuario->tipo === 'barbeiro' && $produto->barbearia_id === $usuario->barbearia_id)) {
            $produto->delete();
            return redirect()->route('produtos.index')->with('success', 'Produto deletado com sucesso!');
        }

        abort(403, 'Acesso negado');
    }
}
