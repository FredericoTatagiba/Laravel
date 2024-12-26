<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscountFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "price"=> "required|numeric|min:0",
            "discount"=> "required|numeric|min:0|max:50",
        ];
    }
    public function messages(): array
    {
        return [
            "price.required"=> "O preço é obrigatório.",
            "price.numeric"=> "O preço deve ser um número.",
            "price.min"=> "O preço deve ser no mínimo 0.",
            "discount.required"=> "O desconto é obrigatório.",
            "discount.numeric"=> "O desconto deve ser um número.",
            "discount.min"=> "O desconto deve ser no mínimo 0.",
            "discount.max"=> "O desconto deve ser no máximo 50.",
        ];
    }
}
