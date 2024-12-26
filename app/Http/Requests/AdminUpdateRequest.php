<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'=> 'nullable|string|min:3|max:255',
            'email'=> 'nullable|email|unique:admins',
            'password'=> 'nullable|min:8'
        ];
    } 

    public function messages(){
        return [
            'name.min'=> 'O nome tem de ter no minimo :min caracteres',
            'name.max' =>'O nome não pode ter mais de :max caracteres',
            'email.email'=> 'Insira um email válido',
            'password.min'=> 'A senha tem de ter pelo menos 8 caracteres',
        ];
    }
}
