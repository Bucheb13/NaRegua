<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produto;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        Produto::create([
            'barbearia_id' => 1,
            'nome' => 'Pomada Modeladora',
            'descricao' => 'Pomada para cabelo',
            'preco' => 25.00,
            'quantidade_estoque' => 50,
        ]);

        Produto::create([
            'barbearia_id' => 1,
            'nome' => 'Shampoo Masculino',
            'descricao' => 'Shampoo hidratante',
            'preco' => 30.00,
            'quantidade_estoque' => 40,
        ]);
    }
}

