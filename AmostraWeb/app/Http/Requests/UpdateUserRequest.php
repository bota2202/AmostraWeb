<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'estabelecimento_id'=>['integer','required','exists:estabelecimentos,id'],
            'name'=>['required','string','max:255'],
            'email'=>['required','email','max:255'],
            'cargo'=>['required','string','max:100'],
        ];
    }
}
