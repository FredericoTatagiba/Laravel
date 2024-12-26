<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'=> 'required|string|min:3|max:255',
            'email'=> 'required|email|unique:admins',
            'password'=> 'required|min:8'
        ];
    } 

    public function messages(){
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.min'=> 'O nome tem de ter no minimo :min caracteres',
            'name.max' =>'O nome não pode ter mais de :max caracteres',
            'email.required'=> 'O email é obrigatório',
            'email.email'=> 'Insira um email válido',
            'email.unique'=> 'Email já cadastrado',
            'password.min'=> 'A senha tem de ter pelo menos 8 caracteres',
        ];
    }
}
