<?php

namespace App\Http\Controllers;

use App\Models\Barbearia;
use App\Models\Produto;
use App\Models\Servico;
use App\Models\Venda;
use App\Models\VendaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BarbeariaController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome'             => 'required|string|max:255',
            'responsavel_nome' => 'required|string|max:255',
            'cnpj'             => 'nullable|string|max:25',
            'telefone'         => 'nullable|string|max:25',
            'email'            => 'nullable|email|max:255',
            'endereco'         => 'nullable|string|max:255',
            'licenca_validade' => 'nullable|date',
            'logo'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        // Upload do logo se veio arquivo
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')
                ->store('barbearias/logos', 'public');
            $validated['logo'] = $path;
        }

        Barbearia::create($validated);

        return redirect()->back()->with('success', 'Barbearia cadastrada com sucesso!');
    }
     /**
     * Atualiza uma Barbearia.
     * - Admin pode editar TODOS os campos
     * - Barbeiro pode editar SOMENTE: responsavel_nome, telefone, email e logo
     */
    public function update(Request $request, Barbearia $barbearia)
    {
        $usuario = $request->user();
        $isAdmin = $usuario && $usuario->tipo === 'admin';

        if ($isAdmin) {
            $validated = $request->validate([
                'nome'             => 'required|string|max:255',
                'responsavel_nome' => 'required|string|max:255',
                'cnpj'             => 'nullable|string|max:25',
                'telefone'         => 'nullable|string|max:25',
                'email'            => 'nullable|email|max:255',
                'endereco'         => 'nullable|string|max:255',
                'licenca_validade' => 'nullable|date',
                'logo'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            ]);
        } else {
            // Barbeiro pode editar: nome, responsável, telefone, email, endereço, logo
            $validated = $request->validate([
                'nome'             => 'required|string|max:255',
                'responsavel_nome' => 'required|string|max:255',
                'telefone'         => 'nullable|string|max:25',
                'email'            => 'nullable|email|max:255',
                'endereco'         => 'nullable|string|max:255',
                'logo'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            ]);
            // bloqueia campos proibidos ao barbeiro
            unset($validated['cnpj'], $validated['licenca_validade']);
        }

        // Substituição de logo (apaga a antiga se existir)
        if ($request->hasFile('logo')) {
            if (!empty($barbearia->logo) && Storage::disk('public')->exists($barbearia->logo)) {
                Storage::disk('public')->delete($barbearia->logo);
            }
            $validated['logo'] = $request->file('logo')->store('barbearias/logos', 'public');
        }

        $barbearia->update($validated);

        return back()->with('success', 'Barbearia atualizada com sucesso!');
    }
    public function destroy(Request $request, Barbearia $barbearia)
    {
        $usuario = $request->user();
        if (!$usuario || $usuario->tipo !== 'admin') {
            abort(403, 'Apenas administradores podem excluir barbearias.');
        }

        DB::transaction(function () use ($barbearia) {
            // 1) Apagar arquivo de LOGO (se existir)
            if (!empty($barbearia->logo) && Storage::disk('public')->exists($barbearia->logo)) {
                Storage::disk('public')->delete($barbearia->logo);
            }

            // 2) VENDAS e VENDAS_ITENS
            $vendaIds = Venda::where('barbearia_id', $barbearia->id)->pluck('id');
            if ($vendaIds->isNotEmpty()) {
                VendaItem::whereIn('venda_id', $vendaIds)->delete();
                Venda::whereIn('id', $vendaIds)->delete();
            }

            // 3) AGENDAMENTOS
            $barbearia->agendamentos()->delete();

            // 4) PRODUTOS
            Produto::where('barbearia_id', $barbearia->id)->delete();

            // 5) SERVIÇOS
            Servico::where('barbearia_id', $barbearia->id)->delete();

            // 6) USUÁRIOS (clientes/barbeiros daquela barbearia)
            $barbearia->usuarios()->delete();

            // 7) BARBEARIA
            $barbearia->delete();
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Barbearia e todos os dados relacionados foram excluídos com sucesso.');
    }
}
