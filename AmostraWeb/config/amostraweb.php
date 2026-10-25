<?php

$crud = ['visualizar', 'criar', 'editar', 'excluir'];

return [
    'name' => 'AmostraWeb',
    'logo_azul' => 'https://images.tcdn.com.br/files/859641/themes/653/img/settings/Lojas-360-Azul-2.png',
    'logo_laranja' => 'Imagens/logo_laranja.png',

    // Nomes dos módulos (na ordem em que aparecem nas telas de permissões)
    'modulos' => [
        'amostras' => 'Amostras',
        'solicitacoes_prazo' => 'Solicitações de prazo',
        'clientes' => 'Clientes',
        'dashboard' => 'Dashboard',
        'estabelecimentos' => 'Estabelecimentos',
        'pedidos' => 'Pedidos',
        'permissoes' => 'Permissões e grupos',
        'produtos' => 'Produtos',
        'usuarios' => 'Usuários',
        'logs' => 'Logs',
    ],

    // Nomes das ações (na ordem em que aparecem)
    'acoes' => [
        'visualizar' => 'Visualizar',
        'criar' => 'Criar',
        'editar' => 'Editar',
        'excluir' => 'Excluir',
        'aprovar' => 'Aprovar',
    ],

    /*
     * Permissões padrão do sistema: módulo => ações.
     * São criadas pela migration "insert_permissoes_padrao".
     * Se mudar esta lista, rode Permissao::sincronizarPadrao() (ex.: numa nova migration).
     */
    'permissoes_padrao' => [
        'amostras' => $crud,
        'solicitacoes_prazo' => ['visualizar', 'criar', 'aprovar', 'excluir'],
        'clientes' => $crud,
        'dashboard' => ['visualizar'],
        'estabelecimentos' => $crud,
        'pedidos' => $crud,
        'permissoes' => $crud,
        'produtos' => $crud,
        'usuarios' => $crud,
        'logs' => ['visualizar'],
    ],
];
