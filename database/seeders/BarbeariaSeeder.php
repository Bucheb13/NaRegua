<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barbearia;
use Carbon\Carbon;

class BarbeariaSeeder extends Seeder
{
    public function run(): void
    {
        Barbearia::create([
            'nome' => 'Barbearia NaRegua',
            'telefone' => '11 99999-9999',
            'email' => 'contato@naregua.com',
            'endereco' => 'Rua Principal, 123',
            'responsavel_nome' => 'Bucheb Admin',
            'cnpj' => '00.000.000/0000-00',
            'licenca_status' => 'ativa',
            'licenca_validade' => Carbon::now()->addYear(), // válida por 1 ano
            'logo' => null,
        ]);
    }
}
