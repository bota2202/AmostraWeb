<?php

namespace Database\Seeders;

use App\Models\Estabelecimento;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Gestores e representantes de exemplo.
 * Todos usam a mesma senha de desenvolvimento: senha123
 */
class UsuarioExemploSeeder extends Seeder
{
    public function run(): void
    {
        $senha = 'senha123';

        $usuarios = [
            // nome, código, cargo, estabelecimento, ativo
            ['Ana Ribeiro', 'ana.ribeiro', 'admin', 1, true],
            ['Carla Mendes', 'carla.mendes', 'gestor', 1, true],
            ['Rafael Souza', 'rafael.souza', 'gestor', 2, true],
            ['Bruno Lima', 'bruno.lima', 'representante', 1, true],
            ['Juliana Costa', 'juliana.costa', 'representante', 1, true],
            ['Marcos Oliveira', 'marcos.oliveira', 'representante', 2, true],
            ['Fernanda Alves', 'fernanda.alves', 'representante', 2, true],
            ['Lucas Pereira', 'lucas.pereira', 'representante', 3, true],
            ['Patrícia Rocha', 'patricia.rocha', 'representante', 3, false],
        ];

        foreach ($usuarios as [$nome, $codigo, $cargo, $estabelecimento, $ativo]) {
            User::create([
                'name' => $nome,
                'email' => $codigo.'@lojas360.test',
                'codigo_usuario' => $codigo,
                'cargo' => $cargo,
                'estabelecimento_id' => $estabelecimento,
                'status' => $ativo,
                'password' => $senha,
            ]);
        }

        // Gestores responsáveis pelos estabelecimentos
        $carla = User::where('codigo_usuario', 'carla.mendes')->value('id');
        $rafael = User::where('codigo_usuario', 'rafael.souza')->value('id');

        Estabelecimento::whereKey(1)->update(['gestor_id' => $carla]);
        Estabelecimento::whereKey([2, 3])->update(['gestor_id' => $rafael]);
    }
}
