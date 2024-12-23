<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderFormRequest extends FormRequest
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
            "delivery_address" => "required|string|max:255",
            "products" => "required|array", // Lista de produtos
            "products.*.id" => "required|integer|exists:products,id",
            "products.*.quantity" => "required|integer|min:1",
        ];
    }
}
