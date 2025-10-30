<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        Usuario::create([
            'barbearia_id' => 1,
            'nome' => 'Bucheb Admin',
            'email' => 'admin@naregua.com',
            'senha' => Hash::make('123456'),
            'tipo' => 'admin',
            'telefone' => '11999999999',
            'status' => 'ativo',
        ]);

        // Barbeiros
        Usuario::create([
            'barbearia_id' => 1,
            'nome' => 'Barbeiro',
            'email' => 'barbeiro@naregua.com',
            'senha' => Hash::make('123456'),
            'tipo' => 'barbeiro',
            'telefone' => '11988888888',
            'especialidade' => 'Cortes Masculinos',
            'status' => 'ativo',
        ]);

        Usuario::create([
            'barbearia_id' => 1,
            'nome' => 'Carlos Barbeiro',
            'email' => 'barber@naregua.com',
            'senha' => Hash::make('123456'),
            'tipo' => 'barbeiro',
            'telefone' => '11977777777',
            'especialidade' => 'Barba / Navalha',
            'status' => 'ativo',
        ]);

        // Clientes
        Usuario::create([
            'barbearia_id' => 1,
            'nome' => 'Cliente Teste',
            'email' => 'cliente@naregua.com',
            'senha' => Hash::make('123456'),
            'tipo' => 'cliente',
            'telefone' => '11966666666',
            'status' => 'ativo',
        ]);
    }
}
