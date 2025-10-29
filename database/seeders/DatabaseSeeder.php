<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BarbeariaSeeder::class,
            UsuarioSeeder::class,
            BarbeiroSeeder::class,
            ClienteSeeder::class,
            ServicoSeeder::class,
            ProdutoSeeder::class,
            AgendamentoSeeder::class,
            VendaSeeder::class,
        ]);
    }
    
}
