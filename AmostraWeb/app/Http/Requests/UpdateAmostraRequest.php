<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Edição da amostra em avaliação: só produto e quantidade.
 * - pedido: não pode ser trocado (RN024)
 * - status: botões Aprovar / Reprovar / Cancelar (AmostraStatusController)
 * - prazo: por solicitação de prazo aprovada (RN008)
 */
class UpdateAmostraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'produto_id' => ['required', 'integer', Rule::exists('produtos', 'id')],
            'quantidade' => ['required', 'integer', 'min:1'],
        ];
    }
}
