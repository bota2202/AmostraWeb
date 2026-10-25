<?php

/*
|--------------------------------------------------------------------------
| Configurações gerais do AmostraWeb (RN021 / RNF010)
|--------------------------------------------------------------------------
| Nome, logotipos, módulos e permissões ficam centralizados aqui, para que
| possam ser alterados sem mexer no código-fonte.
*/

$crud = ['visualizar', 'criar', 'editar', 'excluir'];

return [
    'name' => 'AmostraWeb',
    'empresa' => 'Lojas 360',
    'logo_azul' => 'https://images.tcdn.com.br/files/859641/themes/653/img/settings/Lojas-360-Azul-2.png',
    'logo_laranja' => 'Imagens/logo_laranja.png',

    // Dias de antecedência para avisar que o prazo de avaliação está vencendo (RF030)
    'dias_alerta_prazo' => 7,

    // Itens por página nas listagens
    'itens_por_pagina' => 20,

    // Nomes dos módulos (na ordem em que aparecem nas telas de permissões)
    'modulos' => [
        'pedidos' => 'Pedidos',
        'amostras' => 'Amostras',
        'solicitacoes_prazo' => 'Solicitações de prazo',
        'clientes' => 'Clientes',
        'produtos' => 'Produtos',
        'estabelecimentos' => 'Estabelecimentos',
        'usuarios' => 'Usuários',
        'permissoes' => 'Permissões e grupos',
        'logs' => 'Logs',
    ],

    // Nomes das ações (na ordem em que aparecem)
    'acoes' => [
        'visualizar' => 'Visualizar',
        'criar' => 'Criar',
        'editar' => 'Editar',
        'cancelar' => 'Cancelar',
        'aprovar' => 'Aprovar',
        'importar' => 'Importar',
        'excluir' => 'Excluir',
    ],

    /*
     * Permissões padrão do sistema: módulo => ações.
     * São sincronizadas pelas migrations (Permissao::sincronizarPadrao()).
     * Se mudar esta lista, crie uma migration que chame sincronizarPadrao().
     */
    'permissoes_padrao' => [
        // pedidos não são excluídos: todo pedido tem pelo menos uma amostra (RN023)
        'pedidos' => ['visualizar', 'criar', 'editar'],
        // editar = alterar dados e registrar aprovação/reprovação do cliente
        'amostras' => ['visualizar', 'criar', 'editar', 'cancelar', 'excluir'],
        // aprovar = decidir solicitações (gestor do estabelecimento ou administrador - RN008)
        'solicitacoes_prazo' => ['visualizar', 'criar', 'aprovar', 'excluir'],
        'clientes' => [...$crud, 'importar'],
        'produtos' => [...$crud, 'importar'],
        'estabelecimentos' => $crud,
        'usuarios' => $crud,
        'permissoes' => $crud,
        'logs' => ['visualizar'],
    ],
];
