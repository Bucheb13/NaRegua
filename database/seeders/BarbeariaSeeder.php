<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Barbearia;

class BarbeariaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Barbearia::create([
            'nome' => 'Barbearia NaRegua',
            'telefone' => '11 99999-9999',
            'email' => 'contato@naregua.com',
            'endereco' => 'Rua Principal, 123',
        ]);
    }
}
