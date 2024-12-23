<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductFormRequest extends FormRequest
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
            "name"=> "required|string|min:5|max:255",
            "stock"=> "required|integer|min:0",
            "price"=> "required|numeric|min:0.01",
        ];
    }

    public function messages(): array{
        return [
            "name.required"=> "O nome do produto é obrigatório.",
            "name.min"=> "O nome não pode ter menos de :min caracteres",
            "name.max"=> "O nome não pode ter mais de :max caracteres",
            "stock.required"=> "Qauntidade em estoque é obrigatória",
            "stock.integer"=> "Quantidade em estoque deve ser um número inteiro",
            "stock.min"=> "Quantidade em estoque não pode ser negativa",
            "price.required"=> "Preço do produto é obrigatório",
            "price.min"=> "Preço do produto não pode ser menor que :min",
        ];
    }
}
