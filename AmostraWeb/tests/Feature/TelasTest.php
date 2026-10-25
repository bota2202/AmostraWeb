<?php

namespace Tests\Feature;

use App\Models\Log;
use App\Models\Solicitacao_Prazo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Abre todas as telas para garantir que nenhuma quebra (por perfil).
 */
class TelasTest extends TestCase
{
    use RefreshDatabase;

    public function test_todas_as_telas_abrem_para_o_administrador(): void
    {
        $admin = $this->criarUsuario('admin');
        $vendedor = $this->criarUsuario('representante');
        $pedido = $this->criarPedido($vendedor, ['-2 days', '+3 days']);
        $amostra = $pedido->amostras->first();
        Solicitacao_Prazo::create([
            'amostra_id' => $amostra->id,
            'solicitante_id' => $vendedor->id,
            'justificativa' => 'Cliente pediu mais tempo.',
            'prazo_atual' => $amostra->prazo_avaliacao,
            'novo_prazo' => today()->addDays(10),
            'situacao' => 'pendente',
            'data_solicitacao' => now(),
        ]);
        $grupo = $this->criarGrupo('Vendedores', $this->permissoesVendedor(), [$vendedor]);
        $cliente = $pedido->cliente;
        $produto = $amostra->produto;
        $estabelecimento = $vendedor->estabelecimento;
        $log = Log::latest('id')->first();

        $telas = [
            '/dashboard', '/perfil', '/perfil/editar',
            '/pedidos', '/pedidos/create', "/pedidos/{$pedido->id}", "/pedidos/{$pedido->id}/edit",
            '/amostras', '/amostras/create', "/amostras/{$amostra->id}", "/amostras/{$amostra->id}/edit",
            '/amostras?prazo=vencido', '/amostras?status=em_analise&busca=000',
            '/solicitacoes-prazo', '/solicitacoes-prazo?aba=todas',
            '/clientes', '/clientes/create', "/clientes/{$cliente->id}", "/clientes/{$cliente->id}/edit", '/clientes/importar',
            '/produtos', '/produtos/create', "/produtos/{$produto->id}", "/produtos/{$produto->id}/edit", '/produtos/importar',
            '/estabelecimentos', '/estabelecimentos/create', "/estabelecimentos/{$estabelecimento->id}", "/estabelecimentos/{$estabelecimento->id}/edit",
            '/usuarios', '/usuarios/create', "/usuarios/{$vendedor->id}", "/usuarios/{$vendedor->id}/edit", "/usuarios/{$admin->id}/edit",
            '/grupos', '/grupos/create', "/grupos/{$grupo->id}", "/grupos/{$grupo->id}/edit",
            '/logs', "/logs/{$log->id}",
        ];

        foreach ($telas as $tela) {
            $this->actingAs($admin)->get($tela)->assertOk();
        }
    }

    public function test_telas_do_vendedor(): void
    {
        $vendedor = $this->criarUsuario('representante', permissoes: $this->permissoesVendedor());
        $pedido = $this->criarPedido($vendedor);
        $amostra = $pedido->amostras->first();

        foreach (['/dashboard', '/pedidos', '/pedidos/create', "/pedidos/{$pedido->id}", '/amostras', "/amostras/{$amostra->id}", '/solicitacoes-prazo'] as $tela) {
            $this->actingAs($vendedor)->get($tela)->assertOk();
        }
    }

    public function test_pagina_404_em_portugues(): void
    {
        $admin = $this->criarUsuario('admin');

        $this->actingAs($admin)->get('/pedidos/99999')->assertNotFound()->assertSee('Página não encontrada');
    }
}
