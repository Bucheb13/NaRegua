<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Servico;

class ServicoSeeder extends Seeder
{
    public function run(): void
    {
        Servico::create([
            'barbearia_id' => 1,
            'nome' => 'Corte Simples',
            'descricao' => 'Corte de cabelo rápido e estiloso',
            'preco' => 35.00,
            'duracao_minutos' => 30,
        ]);

        Servico::create([
            'barbearia_id' => 1,
            'nome' => 'Barba Completa',
            'descricao' => 'Barba com navalha e hidratação',
            'preco' => 40.00,
            'duracao_minutos' => 40,
        ]);

        Servico::create([
            'barbearia_id' => 1,
            'nome' => 'Corte + Barba',
            'descricao' => 'Pacote completo de cabelo e barba',
            'preco' => 70.00,
            'duracao_minutos' => 60,
        ]);
    }
}
