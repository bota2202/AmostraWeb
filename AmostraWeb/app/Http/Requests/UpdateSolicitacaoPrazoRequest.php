<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSolicitacaoPrazoRequest extends FormRequest
{
    protected $errorBag = 'decisao';

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
            'situacao' => ['required', 'in:aprovada,reprovada'],
            'motivo_aprovacao_reprovacao' => ['nullable', 'required_if:situacao,reprovada', 'string', 'max:1000'],
        ];
    }
}
