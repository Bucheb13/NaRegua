<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\VendaItem;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VendaController extends Controller
{
    public function index()
    {
        $produtos = Produto::orderBy('nome')->get();
        $vendas = Venda::with('cliente')->orderBy('id', 'desc')->get();
    
        return view('pages.vendas.index', compact('vendas', 'produtos'));
    }
    

    public function create()
    {
        $produtos = Produto::all();
        return view('pages.vendas.index', compact('produtos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'nullable|exists:usuarios,id',
            'produtos' => 'nullable|array',
            'barbearia_id' => 'nullable|exists:barbearias,id', // permite passar barbearia quando admin
        ]);

        // Recebe o payload produtos[<id>][quantidade]
        $produtosInput = $request->input('produtos', []);

        // Monta somente os itens com quantidade > 0
        $itensEscolhidos = [];
        foreach ($produtosInput as $produtoId => $data) {
            $q = intval(data_get($data, 'quantidade', 0));
            if ($q > 0) {
                $itensEscolhidos[$produtoId] = $q;
            }
        }

        if (empty($itensEscolhidos)) {
            return redirect()->back()->with('error', 'Escolha ao menos um produto com quantidade maior que zero.');
        }

        // determina barbearia_id: 1) request, 2) usuário logado, 3) fallback (ajuste conforme seu caso)
        $barbeariaId = $request->input('barbearia_id')
            ?? $request->user()?->barbearia_id
            ?? 1;

        DB::beginTransaction();
        try {
            // Cria venda com valor_total temporário e barbearia_id
            $venda = Venda::create([
                'barbearia_id' => $barbeariaId,
                'cliente_id' => $request->input('cliente_id') ?: null,
                'valor_total' => 0,
                'data_venda' => now(),
            ]);

            $total = 0;

            foreach ($itensEscolhidos as $produtoId => $quantidade) {
                // trava o produto para atualização segura
                $produto = Produto::lockForUpdate()->find($produtoId);
                if (!$produto) {
                    throw new \Exception("Produto #{$produtoId} não encontrado.");
                }

                if ($produto->quantidade_estoque < $quantidade) {
                    throw new \Exception("Estoque insuficiente para o produto {$produto->nome} (disponível: {$produto->quantidade_estoque}).");
                }

                $precoUnit = $produto->preco;
                $subtotal = $precoUnit * $quantidade;
                $total += $subtotal;

                // cria item da venda usando relação (respeita $fillable do VendaItem)
                $venda->itens()->create([
                    'produto_id' => $produto->id,
                    'quantidade' => $quantidade,
                    'preco_unitario' => $precoUnit,
                    'subtotal' => $subtotal,
                ]);

                // decrementa estoque e salva
                $produto->quantidade_estoque -= $quantidade;
                $produto->save();
            }

            // atualiza total da venda
            $venda->valor_total = $total;
            $venda->save();

            DB::commit();

            return redirect()->route('vendas.index')->with('success', 'Venda registrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao registrar venda: '.$e->getMessage(), [
                'produtos' => $produtosInput,
                'usuario_id' => $request->user()?->id,
            ]);
            return redirect()->back()->with('error', 'Erro ao registrar venda: ' . $e->getMessage());
        }
    }
    public function show($id)
    {
        $venda = Venda::with(['itens.produto', 'cliente'])->findOrFail($id);
        return view('pages.vendas.index', compact('venda'));
    }

    public function destroy($id)
    {
        $venda = Venda::findOrFail($id);
        $venda->delete();
        return redirect()->route('vendas.index')->with('success', 'Venda excluída com sucesso!');
    }
}
