<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstabelecimentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('estabelecimentos')->insert([
            'razao_social'=>'Estabelecimento padrão',
            'uf'=>'sp',
            'cidade'=>'limeira',
            'cnpj'=>'00.000.000/0000-01',
        ]);
    }
}
