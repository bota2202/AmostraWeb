<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitacaoPrazoRequest extends FormRequest
{
    // Erros ficam separados dos outros formulários da tela da amostra
    protected $errorBag = 'solicitacao';

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
        $prazoAtual = $this->route('amostra')?->prazo_avaliacao;

        return [
            'novo_prazo' => [
                'required',
                'date',
                'after:today',
                // prorrogação: o novo prazo precisa ser depois do prazo atual
                function (string $atributo, mixed $valor, \Closure $falhar) use ($prazoAtual) {
                    if ($prazoAtual && strtotime($valor) <= $prazoAtual->getTimestamp()) {
                        $falhar('O novo prazo deve ser depois do prazo atual ('.$prazoAtual->format('d/m/Y').').');
                    }
                },
            ],
            'justificativa' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'novo_prazo.after' => 'O novo prazo deve ser uma data futura.',
            'justificativa.min' => 'Explique a necessidade com pelo menos 10 caracteres.',
        ];
    }
}
