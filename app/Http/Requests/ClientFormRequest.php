<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientFormRequest extends FormRequest
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
            'name'=> 'required|string|max:255',
            'cpf'=> 'required|string|digits:11',
            'email'=> 'required|email|unique:users'
        ];
    }

    public function messages(): array{
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' =>'O nome não pode ter mais de :max caracteres',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.digits' => 'O CPF deve ter 11 dígitos.',
            'email.required'=> 'O email é obrigatório',
            'email.email'=> 'Insira um email válido',
            'email.unique'=> 'Email já cadastrado'
        ];
    }
}
