<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barbeiro;
use App\Models\Usuario;

class BarbeiroSeeder extends Seeder
{
    public function run(): void
    {
        $usuariosBarbeiros = Usuario::where('tipo','barbeiro')->get();
        foreach ($usuariosBarbeiros as $user) {
            Barbeiro::create([
                'usuario_id' => $user->id,
                'especialidade' => 'Corte masculino',
                'status' => 'ativo',
            ]);
        }
    }
}
