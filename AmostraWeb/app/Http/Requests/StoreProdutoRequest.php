<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProdutoRequest extends FormRequest
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
            'codigo_produto' => ['required','unique:produtos,codigo_produto','digits:6'],
            'descricao' => ['required','string','max:255','min:3'],
            'familia' => ['nullable','string','max:20'],
            'subfamilia' => ['nullable','string','max:20'],
            'grupo' => ['nullable','string','max:20'],
            'tipo' => ['nullable','string','max:20'],
            'ncm' => ['nullable','string','max:50'],
            'aplicacao_entrada' => ['nullable','string','max:20'],
            'aplicacao_saida' => ['nullable','string','max:20'],
            'origem_mercadoria' => ['nullable','string','max:20'],
            'controle_verba' => ['nullable','string','max:20'],
            'status' => ['boolean','nullable'],
            'marca' => ['nullable','string','max:255'],
            'data_revisao' => ['nullable','date'],
        ];
    }
}
