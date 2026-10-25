<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Itens por página nas listagens (config amostraweb.itens_por_pagina).
     */
    protected function porPagina(): int
    {
        return (int) config('amostraweb.itens_por_pagina', 20);
    }

    /**
     * Bloqueia o acesso a pedidos/amostras fora do escopo do usuário (RN017).
     */
    protected function autorizarEscopo(object $registro): void
    {
        abort_unless(
            $registro->visivelPara(auth()->user()),
            403,
            'Este registro não pertence ao seu escopo de visualização.'
        );
    }
}
