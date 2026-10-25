<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Uso: php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        $this->call([
            EstabelecimentoSeeder::class,
            UserSeeder::class,

            // dados de exemplo
            UsuarioExemploSeeder::class,
            ClienteSeeder::class,
            ProdutoSeeder::class,
            PedidoSeeder::class,
            AmostraSeeder::class,
            SolicitacaoPrazoSeeder::class,
            GrupoSeeder::class,
        ]);
    }
}
