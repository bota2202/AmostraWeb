<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitacaoPrazoRequest extends FormRequest
{
    // Erros ficam separados dos outros formulários da tela da amostra
    protected $errorBag = 'solicitacao';

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
            'novo_prazo' => ['required', 'date', 'after:today'],
            'justificativa' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }
}
