<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "delivery_address" => "required|string|max:500",
            "products" => "required|array", // Lista de produtos
            "products.*.id" => "required|integer|exists:products,id",
            "products.*.quantity" => "required|integer|min:1",
        ];
    }

    public function messages(): array{
        return [
            "delivery_address.required"=> "O endereço de entrega é obrigatório.",
            "delivery_address.max"=> "O endereco de entrega não pode ter mais de :max caracteres",
            "products.required"=> "A lista de produtos é obrigatória",
            "products.*.id.required"=> "O ID do produto é obrigatório",
            "products.*.id.exists"=> "O produto com ID :input não foi encontrado",
            "products.*.quantity.required"=> "A quantidade do produto é obrigatória",
            "products.*.quantity.integer"=> "A quantidade do produto deve ser um número inteiro",
            "products.*.quantity.min"=> "A quantidade do produto não pode ser menor que :min",
        ];
    }
}
