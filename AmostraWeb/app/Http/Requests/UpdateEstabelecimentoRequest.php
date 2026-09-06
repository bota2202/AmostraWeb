<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEstabelecimentoRequest extends FormRequest
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
            'razao_social'=>['required','string','max:255'],
            'bairro'=>['nullable','string','max:255'],
            'rua'=>['nullable','string','max:255'],
            'numero'=>['nullable','string','max:255'],
            'cep'=>['nullable','regex:/^[0-9]{5}-[0-9]{3}$/'],
            'gestor_id'=>['nullable','integer','exists:users,id'],
        ];
    }
}
