<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAmostraRequest extends FormRequest
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
            // toda amostra pertence a um pedido em aberto (RN005)
            'pedido_id' => ['required', 'integer', Rule::exists('pedidos', 'id')->where('status', 'aberto')],
            'produto_id' => ['required', 'integer', Rule::exists('produtos', 'id')->where('status', true)],
            'quantidade' => ['required', 'integer', 'min:1'],
            'prazo_avaliacao' => ['required', 'date', 'after_or_equal:today'],
        ];
    }
}
