<?php

namespace App\Models\Concerns;

use App\Models\Log;
use BackedEnum;
use DateTimeInterface;

/**
 * Registra na auditoria (tabela logs) toda criação, alteração e exclusão
 * do model (RN015 / RN020): usuário, data e hora, ação e campos alterados.
 *
 * No model:
 *   use Auditavel;
 *   protected string $moduloAuditoria = 'amostras';
 *
 * Para anexar um comentário à próxima alteração (ex.: motivo do cancelamento):
 *   $amostra->observacaoAuditoria = 'Cliente desistiu';
 */
trait Auditavel
{
    // comentário opcional gravado junto com a próxima alteração
    public ?string $observacaoAuditoria = null;

    // campos que nunca entram na auditoria
    protected static array $camposNaoAuditados = ['updated_at', 'created_at', 'remember_token', 'email_verified_at'];

    // campos sensíveis: registra que mudou, mas não o valor
    protected static array $camposSigilosos = ['password'];

    protected static function bootAuditavel(): void
    {
        static::created(function ($model) {
            if (Log::auditoriaPausada()) {
                return;
            }

            Log::registrar(
                $model,
                'criar',
                observacao: $model->observacaoAuditoria ?? 'Registro criado',
                valorNovo: self::resumoAuditoria($model->getAttributes()),
            );
            $model->observacaoAuditoria = null;
        });

        static::updated(function ($model) {
            if (Log::auditoriaPausada()) {
                return;
            }

            // um registro por campo, todos no mesmo lote (aparecem como uma ação só)
            Log::emLote(function () use ($model) {
                foreach ($model->getChanges() as $campo => $novo) {
                    if (in_array($campo, self::$camposNaoAuditados, true)) {
                        continue;
                    }

                    $sigiloso = in_array($campo, self::$camposSigilosos, true);

                    Log::registrar(
                        $model,
                        $campo === 'status' ? 'alterar_status' : 'editar',
                        campo: $campo,
                        valorAnterior: $sigiloso ? '••••••' : self::valorAuditoria($model->getOriginal($campo)),
                        valorNovo: $sigiloso ? '••••••' : self::valorAuditoria($novo),
                        observacao: $model->observacaoAuditoria ?? ($sigiloso ? 'Senha alterada' : null),
                    );
                }
            });
            $model->observacaoAuditoria = null;
        });

        static::deleted(function ($model) {
            if (Log::auditoriaPausada()) {
                return;
            }

            Log::registrar(
                $model,
                'excluir',
                observacao: $model->observacaoAuditoria ?? 'Registro excluído',
                valorAnterior: self::resumoAuditoria($model->getAttributes()),
            );
        });
    }

    public function getModuloAuditoria(): string
    {
        return property_exists($this, 'moduloAuditoria') ? $this->moduloAuditoria : $this->getTable();
    }

    /**
     * Converte o valor para texto legível na auditoria.
     */
    protected static function valorAuditoria(mixed $valor): ?string
    {
        return match (true) {
            $valor === null => null,
            is_bool($valor) => $valor ? 'Sim' : 'Não',
            $valor instanceof DateTimeInterface => $valor->format('Y-m-d H:i:s'),
            $valor instanceof BackedEnum => (string) $valor->value,
            is_array($valor) => json_encode($valor, JSON_UNESCAPED_UNICODE),
            default => (string) $valor,
        };
    }

    /**
     * Retrato do registro (sem campos sigilosos) para criação e exclusão.
     */
    protected static function resumoAuditoria(array $atributos): string
    {
        $dados = array_diff_key($atributos, array_flip([...self::$camposNaoAuditados, ...self::$camposSigilosos]));

        return json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
