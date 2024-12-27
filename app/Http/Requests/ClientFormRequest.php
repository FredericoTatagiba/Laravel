<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class ClientFormRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'name'=> 'required|string|max:255',
            'cpf'=> 'required|cpf',
            'email' => 'required|email|unique:clients,email,',
        ];
    }

    public function messages(): array{
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' =>'O nome não pode ter mais de :max caracteres',
            'cpf.required' => 'O CPF é obrigatório.',
            'email.required'=> 'O email é obrigatório',
            'email.email'=> 'Insira um email válido',
            'email.unique'=> 'Email já cadastrado'
        ];
    }
}
