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
            'nome' => 'BUcheb Admin',
            'email' => 'admin@naregua.com',
            'senha' => Hash::make('143964'),
            'tipo' => 'admin',
            'telefone' => '11999999999',
        ]);

        // Barbeiros
        Usuario::create([
            'barbearia_id' => 1,
            'nome' => 'João Barbeiro',
            'email' => 'joao@naregua.com',
            'senha' => Hash::make('123456'),
            'tipo' => 'barbeiro',
            'telefone' => '11988888888',
        ]);

        Usuario::create([
            'barbearia_id' => 1,
            'nome' => 'Carlos Barbeiro',
            'email' => 'carlos@naregua.com',
            'senha' => Hash::make('123456'),
            'tipo' => 'barbeiro',
            'telefone' => '11977777777',
        ]);

        // Clientes
        Usuario::create([
            'barbearia_id' => 1,
            'nome' => 'Cliente Teste',
            'email' => 'cliente@naregua.com',
            'senha' => Hash::make('123456'),
            'tipo' => 'cliente',
            'telefone' => '11966666666',
        ]);
    }
}
