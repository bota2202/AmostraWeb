<?php

namespace App\Models;

use App\Models\Concerns\Auditavel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use Auditavel;

    protected string $moduloAuditoria = 'clientes';

    protected $fillable = [
        'codigo_empresa',
        'razao_social',
        'nome_fantasia',
        'status',
        'tipo',
        'grupo_fiscal',
        'tipo_empresa',
        'grupo_economico',
        'cnpj_cpf',
        'endereco_completo',
        'telefone',
        'email',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function scopeBusca(Builder $query, ?string $termo): Builder
    {
        if (blank($termo)) {
            return $query;
        }

        $digitos = preg_replace('/\D/', '', $termo);

        return $query->where(function (Builder $q) use ($termo, $digitos) {
            $q->where('razao_social', 'like', "%{$termo}%")
                ->orWhere('nome_fantasia', 'like', "%{$termo}%")
                ->orWhere('codigo_empresa', 'like', "%{$termo}%")
                ->orWhere('cnpj_cpf', 'like', "%{$termo}%");

            // permite buscar o CNPJ/CPF só pelos números
            if (strlen($digitos) >= 4) {
                $q->orWhereRaw("REPLACE(REPLACE(REPLACE(cnpj_cpf, '.', ''), '-', ''), '/', '') LIKE ?", ["%{$digitos}%"]);
            }
        });
    }
}
