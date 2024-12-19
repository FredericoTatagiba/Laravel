<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index(){
        //Redirecionamento para a pagina inicial.
        //Toda parte logica fica aqui.
        $name = "Frederico";
        $surname = "Tatagiba";
        $age = 26;
        $born = '20/10/1998';
        $data = [
            'name'=> $name,
            'surname'=> $surname,
            'age'=> $age,
            'born'=> $born
        ];

        return view("welcome", $data);
    }

    public function exit(){
        return view("exit");
    }

    public function users( Request $request){
        $data = [
            'id' => $request->id
        ];
        return view("users", $data);
    }
}
