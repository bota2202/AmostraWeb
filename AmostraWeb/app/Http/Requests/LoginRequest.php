<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class LoginRequest extends FormRequest
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
            'codigo_usuario' => ['string', 'required'],
            'password' => ['string', 'required']
        ];
    }

    #[Override]
    public function messages(): array
    {
        return [
            'codigo_usuario.required' => 'Preencha todos os campos!',
            'codigo_usuario.string' => 'Preencha todos os campos!',
            'password.required' => 'Preencha todos os campos!',
            'password.string' => 'Preencha todos os campos!',
        ];
    }
}
