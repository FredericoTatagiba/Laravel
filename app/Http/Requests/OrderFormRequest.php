<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\OrderProductsFormRequest;

class OrderFormRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            "client_id" => "required|integer|exists:clients,id", // Validar se o cliente existe
            "delivery_address" => "required|string|max:500", // Validar endereço
            "products" => "required|array", // Lista de produtos
            "products.*"=>[new OrderProductsFormRequest]
        ];
    }

    public function messages(): array
    {
        return [
            "client_id.required" => "O ID do cliente é obrigatório.",
            "client_id.exists" => "O cliente com ID :input não foi encontrado.",
            "delivery_address.required" => "O endereço de entrega é obrigatório.",
            "delivery_address.max" => "O endereço de entrega não pode ter mais de :max caracteres.",
            "products.required" => "A lista de produtos é obrigatória.",
            "products.*.id.required" => "O ID do produto é obrigatório.",
            "products.*.id.exists" => "O produto com ID :input não foi encontrado.",
            "products.*.quantity.required" => "A quantidade do produto é obrigatória.",
            "products.*.quantity.integer" => "A quantidade do produto deve ser um número inteiro.",
            "products.*.quantity.min" => "A quantidade do produto não pode ser menor que :min.",
        ];
    }
}

