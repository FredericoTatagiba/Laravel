<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Old___OrderProductsFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "id" => "required|integer|exists:products,id", // Validar se o produto existe
            "quantity" => "required|integer|min:1", // Validar quantidade do produto
        ];
    }

    public function messages(): array
    {
        return [
            "id.required" => "O ID do produto é obrigatório.",
            "id.exists" => "O produto com ID :input não foi encontrado.",
            "quantity.required" => "A quantidade do produto é obrigatória.",
            "quantity.integer" => "A quantidade do produto deve ser um número inteiro.",
            "quantity.min" => "A quantidade do produto não pode ser menor que :min.",
        ];
    }
}
