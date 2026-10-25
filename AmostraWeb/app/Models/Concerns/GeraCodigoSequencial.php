<?php

namespace App\Models\Concerns;

/**
 * Gera o campo "codigo" automaticamente na criação: 000001, 000002, ...
 * (RN001 - pedidos, RN002 - amostras).
 *
 * Crie o registro dentro de DB::transaction() para o lock do próximo
 * código valer até o insert.
 */
trait GeraCodigoSequencial
{
    protected static function bootGeraCodigoSequencial(): void
    {
        static::creating(function ($model) {
            $model->codigo ??= static::proximoCodigo();
        });
    }

    public static function proximoCodigo(): string
    {
        $ultimo = (int) static::lockForUpdate()->max('codigo');
        return str_pad((string) ($ultimo + 1), 6, '0', STR_PAD_LEFT);
    }
}
