<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Edição do pedido. O status não é editável: é calculado pelas amostras.
 */
class UpdatePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * O responsável só pode ser trocado por gestores e administradores (RN004).
     */
    public function podeTrocarResponsavel(): bool
    {
        return ! $this->user()->isVendedor();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $regras = [
            'cliente_id' => ['required', 'integer', 'exists:clientes,id'],
            'observacao' => ['nullable', 'string', 'max:255'],
            'data_emissao' => ['required', 'date'],
        ];

        if ($this->podeTrocarResponsavel()) {
            $regras['user_id'] = ['required', 'integer', Rule::exists('users', 'id')->where('cargo', 'representante')];
        }

        return $regras;
    }

}
