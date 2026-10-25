<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Aba "Permissões" do usuário: permissões extras (além das que vêm dos grupos).
 */
class UpdateUsuarioPermissaoRequest extends FormRequest
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
            'permissoes' => ['nullable', 'array'],
            'permissoes.*' => ['integer', 'exists:permissoes,id'],
        ];
    }
}
