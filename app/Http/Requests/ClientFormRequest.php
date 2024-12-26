<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientFormRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'name'=> 'required|string|max:255',
            'cpf'=> 'required|cpf|digits:11',
            'email' => 'required|email|unique:clients,email,',
        ];
    }

    public function messages(): array{
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' =>'O nome não pode ter mais de :max caracteres',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.cpf'=> 'O CPF é inválido.',
            'cpf.digits' => 'O CPF deve ter 11 dígitos.',
            'email.required'=> 'O email é obrigatório',
            'email.email'=> 'Insira um email válido',
            'email.unique'=> 'Email já cadastrado'
        ];
    }
}
