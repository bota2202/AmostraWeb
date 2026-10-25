<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
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
            'codigo_empresa' => ['required', 'string', 'max:20', 'unique:clientes,codigo_empresa'],
            'razao_social' => ['required', 'string', 'max:255'],
            'nome_fantasia' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'boolean'],
            'tipo' => ['required', 'string', 'max:50'],
            'grupo_fiscal' => ['nullable', 'string', 'max:50'],
            'tipo_empresa' => ['nullable', 'string', 'max:50'],
            'grupo_economico' => ['nullable', 'string', 'max:100'],
            'cnpj_cpf' => ['required', 'regex:/^([0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}|[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}-[0-9]{2})$/', 'unique:clientes,cnpj_cpf'],
            'endereco_completo' => ['nullable', 'string', 'max:500'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
