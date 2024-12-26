<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
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
            "delivery_address" => "nullable|string|max:500",
            "products" => "nullable|array", // Lista de produtos
            "products.*.id" => "nullable|integer|exists:products,id",
            "products.*.quantity" => "nullable|integer|min:1"
        ];
    }

    public function messages(): array{
        return [
            "delivery_address.max"=> "O endereco de entrega não pode ter mais de :max caracteres",
            "products.*.id.exists"=> "O produto com ID :input não foi encontrado",
            "products.*.quantity.integer"=> "A quantidade do produto deve ser um número inteiro",
            "products.*.quantity.min"=> "A quantidade do produto não pode ser menor que :min",
        ];
    }
}
