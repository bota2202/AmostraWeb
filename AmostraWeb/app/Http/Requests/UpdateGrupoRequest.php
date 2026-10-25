<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Salva as três abas do grupo de uma vez: dados, membros e permissões.
 */
class UpdateGrupoRequest extends FormRequest
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
            'nome' => ['required', 'string', 'max:100', Rule::unique('grupos', 'nome')->ignore($this->route('grupo'))],
            'descricao' => ['nullable', 'string', 'max:255'],
            'usuarios' => ['nullable', 'array'],
            'usuarios.*' => ['integer', 'exists:users,id'],
            'permissoes' => ['nullable', 'array'],
            'permissoes.*' => ['integer', 'exists:permissoes,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.unique' => 'Já existe um grupo com esse nome.',
        ];
    }
}
