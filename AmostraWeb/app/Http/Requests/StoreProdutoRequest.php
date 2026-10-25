<?php

namespace App\Http\Requests;

use App\Models\Produto;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProdutoRequest extends FormRequest
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
            // formato do ERP: ex. 110.30949
            'codigo_produto' => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9.\-\/]+$/', 'unique:produtos,codigo_produto'],
            ...self::regrasComuns(),
            'status' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Regras compartilhadas entre cadastro e edição.
     */
    public static function regrasComuns(): array
    {
        $regras = [
            'descricao' => ['required', 'string', 'min:3', 'max:255'],
            'marca' => ['nullable', 'string', 'max:255'],
            'data_revisao' => ['nullable', 'date'],
        ];

        foreach (array_keys(Produto::camposOpcionais()) as $campo) {
            $regras[$campo] = ['nullable', 'string', 'max:255'];
        }

        return $regras;
    }
}
