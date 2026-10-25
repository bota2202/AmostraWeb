<?php

namespace App\Models;

use App\Models\Concerns\Auditavel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Produto comercializado pela empresa (campos do cadastro de produtos do ERP).
 */
class Produto extends Model
{
    use Auditavel;

    protected string $moduloAuditoria = 'produtos';

    /**
     * Campos opcionais, agrupados como na tela do ERP: campo => rótulo.
     * Usado nas telas de cadastro, edição, detalhes e na importação.
     */
    public const SECOES = [
        'Classificação' => [
            'icone' => 'fa-layer-group',
            'campos' => [
                'familia' => 'Família',
                'subfamilia' => 'Subfamília',
                'grupo' => 'Grupo',
                'categoria' => 'Categoria',
                'classe' => 'Classe',
                'tipo' => 'Tipo',
                'id_categoria' => 'ID da categoria',
            ],
        ],
        'Fiscal' => [
            'icone' => 'fa-file-invoice',
            'campos' => [
                'ncm' => 'NCM',
                'nbs' => 'NBS',
                'cest' => 'CEST',
                'anp' => 'ANP',
                'servico' => 'Serviço',
                'origem_mercadoria' => 'Origem da mercadoria',
                'aplicacao_entrada' => 'Aplicação de entrada',
                'aplicacao_saida' => 'Aplicação de saída',
                'controle_verba' => 'Controle de verba',
                'tributacao_municipio' => 'Tributação do município',
            ],
        ],
        'Comercial' => [
            'icone' => 'fa-briefcase',
            'campos' => [
                'ca' => 'C.A. (Certificado de Aprovação)',
                'id_preco' => 'ID do preço',
            ],
        ],
    ];

    protected $fillable = [
        'codigo_produto',
        'descricao',
        'familia',
        'subfamilia',
        'grupo',
        'categoria',
        'nbs',
        'tipo',
        'ncm',
        'servico',
        'cest',
        'anp',
        'aplicacao_entrada',
        'aplicacao_saida',
        'origem_mercadoria',
        'controle_verba',
        'status',
        'marca',
        'data_revisao',
        'classe',
        'ca',
        'id_categoria',
        'id_preco',
        'tributacao_municipio',
    ];

    protected $casts = [
        'status' => 'boolean',
        'data_revisao' => 'date',
    ];

    public function amostras()
    {
        return $this->hasMany(Amostra::class);
    }

    /**
     * Todos os campos opcionais das seções (para validação e formulários).
     *
     * @return array<string, string>
     */
    public static function camposOpcionais(): array
    {
        return array_merge(...array_column(self::SECOES, 'campos'));
    }

    public function scopeBusca(Builder $query, ?string $termo): Builder
    {
        if (blank($termo)) {
            return $query;
        }

        return $query->where(fn (Builder $q) => $q
            ->where('codigo_produto', 'like', "%{$termo}%")
            ->orWhere('descricao', 'like', "%{$termo}%")
            ->orWhere('marca', 'like', "%{$termo}%"));
    }
}
