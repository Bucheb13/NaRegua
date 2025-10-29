<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\Usuario;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $usuariosClientes = Usuario::where('tipo','cliente')->get();
        foreach ($usuariosClientes as $user) {
            Cliente::create([
                'usuario_id' => $user->id,
            ]);
        }
    }
}

