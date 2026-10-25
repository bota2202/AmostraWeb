<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create('pt_BR');
        $faker->seed(360);

        for ($i = 1; $i <= 15; $i++) {
            // os dois últimos são pessoa física (CPF)
            $pessoaFisica = $i > 13;
            $nome = $pessoaFisica ? $faker->name() : $faker->company();

            Cliente::create([
                'codigo_empresa' => 'C'.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                // o Faker já gera o nome com 'Ltda.', 'S.A.' etc.
                'razao_social' => $nome,
                'nome_fantasia' => $pessoaFisica ? null : trim(preg_replace('/\s*(Ltda\.?|S\.A\.?|e Filhos|EIRELI)$/i', '', $nome)),
                'status' => $i !== 12, // um cliente inativo
                'tipo' => $faker->randomElement(['Revenda', 'Consumidor final', 'Distribuidor', 'Construtora']),
                'grupo_fiscal' => $faker->randomElement(['Simples Nacional', 'Lucro Presumido', 'Lucro Real']),
                'tipo_empresa' => $pessoaFisica ? null : $faker->randomElement(['Matriz', 'Filial']),
                'grupo_economico' => $faker->optional(0.3)->randomElement(['Grupo Alfa', 'Grupo Horizonte', 'Grupo Vale']),
                'cnpj_cpf' => $pessoaFisica ? $faker->unique()->cpf() : $faker->unique()->cnpj(),
                'endereco_completo' => $faker->streetAddress().', '.$faker->city().' - '.$faker->stateAbbr().', '.$faker->postcode(),
                'telefone' => $faker->cellphoneNumber(),
                'email' => $faker->unique()->safeEmail(),
            ]);
        }
    }
}
