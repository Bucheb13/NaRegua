<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Venda;
use App\Models\Cliente;
use App\Models\Produto;

class VendaSeeder extends Seeder
{
    public function run(): void
    {
        $cliente = Cliente::first();
        $produto = Produto::first();

        Venda::create([
            'barbearia_id' => 1,
            'cliente_id' => $cliente->id,
            'produto_id' => $produto->id,
            'quantidade' => 2,
            'valor_total' => $produto->preco * 2,
            'data_venda' => now(),
        ]);
    }
}

