<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientUpdateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'name'=> 'nullable|string|max:255',
            'cpf'=> 'nullable|cpf|digits:11',
            'email' => 'nullable|email|unique:clients,email,',
        ];
    }

    public function messages(): array{
        return [
            'name.max' =>'O nome não pode ter mais de :max caracteres',
            'cpf.cpf'=> 'O CPF é inválido.',
            'cpf.digits' => 'O CPF deve ter 11 dígitos.',
            'email.email'=> 'Insira um email válido',
            'email.unique'=> 'Email já cadastrado'
        ];
    }
}
