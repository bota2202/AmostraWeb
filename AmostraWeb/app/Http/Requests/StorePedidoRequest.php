<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Cadastro do pedido junto com as suas amostras (Fluxo das Amostras / RN003).
 * Cada item de "amostras" vira uma amostra com código próprio.
 */
class StorePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Vendedor cadastrando: ele mesmo é o responsável (RN004).
     */
    protected function prepareForValidation(): void
    {
        if ($this->user()->cargo === 'representante') {
            $this->merge(['user_id' => $this->user()->id]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cliente_id' => ['required', 'integer', Rule::exists('clientes', 'id')->where('status', true)],
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')->where('cargo', 'representante')->where('status', true)],
            'observacao' => ['nullable', 'string', 'max:255'],
            'data_emissao' => ['required', 'date'],

            // pelo menos uma amostra (RN003 / RN023)
            'amostras' => ['required', 'array', 'min:1'],
            'amostras.*.produto_id' => ['required', 'integer', Rule::exists('produtos', 'id')->where('status', true)],
            'amostras.*.quantidade' => ['required', 'integer', 'min:1'],
            'amostras.*.prazo_avaliacao' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'amostras.required' => 'Adicione pelo menos um produto ao pedido.',
            'amostras.min' => 'Adicione pelo menos um produto ao pedido.',
            'amostras.*.produto_id.required' => 'Escolha o produto.',
            'amostras.*.quantidade.min' => 'A quantidade deve ser pelo menos 1.',
            'amostras.*.prazo_avaliacao.required' => 'Informe o prazo de avaliação.',
            'amostras.*.prazo_avaliacao.after_or_equal' => 'O prazo não pode ser anterior a hoje.',
            'user_id.required' => 'Escolha o vendedor responsável.',
        ];
    }
}
