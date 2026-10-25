<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Troca da própria senha: exige a senha atual.
 */
class UpdateSenhaPerfilRequest extends FormRequest
{
    // erros ficam separados do formulário de dados pessoais
    protected $errorBag = 'senha';

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
            'senha_atual' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'senha_atual.required' => 'Informe sua senha atual.',
            'senha_atual.current_password' => 'A senha atual está incorreta.',
            'password.required' => 'Informe a nova senha.',
            'password.min' => 'A nova senha deve ter pelo menos 6 caracteres.',
            'password.confirmed' => 'A confirmação não confere com a nova senha.',
        ];
    }
}
