<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Database\Seeder;

class PedidoSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = Cliente::where('status', true)->pluck('id');
        $representantes = User::where('cargo', 'representante')->where('status', true)->get();
        $observacoes = [
            'Cliente pediu entrega no período da manhã.',
            'Amostras para avaliação da equipe de segurança.',
            'Retornar contato após a avaliação.',
            null,
            null,
        ];

        for ($i = 1; $i <= 14; $i++) {
            $representante = $representantes->random();
            $diasAtras = random_int(1, 60);

            Pedido::create([
                'codigo' => str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                'cliente_id' => $clientes->random(),
                'estabelecimento_id' => $representante->estabelecimento_id,
                'user_id' => $representante->id,
                'observacao' => $observacoes[array_rand($observacoes)],
                // pedidos mais antigos já foram fechados
                'status' => $diasAtras > 40 ? 'fechado' : 'aberto',
                'data_emissao' => today()->subDays($diasAtras),
            ]);
        }
    }
}
