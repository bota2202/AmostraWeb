<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Otávio Saturnino da Silva',
            'email' => 'ti5@grupogipe.com.br',
            'cargo' => 'admin',
            'codigo_usuario' => 'otavio.silva',
            'password' => '$2y$12$TN12B3mdglUYzx5NVmbM3.Ntyw2/T.eXFOgqNBmZ0cmWLl0J4.3x2',
            'estabelecimento_id'=>'1',
        ]);
    }
}
