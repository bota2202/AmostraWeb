<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePedidoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'codigo' => ['required', 'digits:6', 'unique:pedidos,codigo'],
            'cliente_id' => ['required', 'integer', 'exists:clientes,id'],
            'estabelecimento_id' => ['required', 'integer', 'exists:estabelecimentos,id'],
            'observacao' => ['nullable', 'string', 'max:255'],
            'data_emissao' => ['required', 'date'],
        ];
    }
}
