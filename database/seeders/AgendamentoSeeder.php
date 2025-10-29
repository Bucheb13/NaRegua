<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Barbeiro;
use App\Models\Servico;

class AgendamentoSeeder extends Seeder
{
    public function run(): void
    {
        $cliente = Cliente::first();
        $barbeiro = Barbeiro::first();
        $servico = Servico::first();

        Agendamento::create([
            'barbearia_id' => 1,
            'cliente_id' => $cliente->id,
            'barbeiro_id' => $barbeiro->id,
            'servico_id' => $servico->id,
            'data_hora' => now()->addDays(1)->setHour(10)->setMinute(0),
            'status' => 'agendado',
        ]);
    }
}

