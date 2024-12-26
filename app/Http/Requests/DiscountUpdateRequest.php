<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscountUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "price"=> "nullable|numeric|min:0|unique:discounts,price",
            "discount"=> "nullable|numeric|min:0|max:50",
        ];
    }
    public function messages(): array
    {
        return [
            "price.numeric"=> "O preço deve ser um número.",
            "price.min"=> "O preço deve ser no mínimo 0.",
            "price.unique"=> "Já existe desconto para este preço.",
            "discount.numeric"=> "O desconto deve ser um número.",
            "discount.min"=> "O desconto deve ser no mínimo 0.",
            "discount.max"=> "O desconto deve ser no máximo 50.",
        ];
    }
}
