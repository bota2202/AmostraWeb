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

        // Estabelecimentos de exemplo
        DB::table('estabelecimentos')->insert([
            [
                'razao_social' => 'Lojas 360 Campinas',
                'cnpj' => '12.345.678/0001-90',
                'uf' => 'sp',
                'cidade' => 'campinas',
                'bairro' => 'Centro',
                'rua' => 'Av. Francisco Glicério',
                'numero' => '1200',
                'cep' => '13012-100',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'razao_social' => 'Lojas 360 Piracicaba',
                'cnpj' => '12.345.678/0002-71',
                'uf' => 'sp',
                'cidade' => 'piracicaba',
                'bairro' => 'Vila Rezende',
                'rua' => 'Rua Benjamin Constant',
                'numero' => '845',
                'cep' => '13405-055',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
