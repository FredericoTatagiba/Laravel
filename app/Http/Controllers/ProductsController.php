<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function insert(Request $r){
        //Valida o que está sendo enviado de acordo com os requesitos
        $validate = $r->validate([
            "name"=> "required|string|max:255",
            "quantity"=> "required|integer|min:0",
            "price"=> "required|numeric|min:0.01",

        ]);
        Product::create($validate);
        return response()->json(['message'=>'Pedido criado com sucesso'],200);
    }

    public function read(Request $r, $id){
        $product = Product::find($id);
        if(!$product){return response()->json(['message'=>'Produto não encontrado', 404]);}
        return response()->json($product);
    }

    public function all(Request $r){
        $product = Product::all();
        return $product;
    }

    public function update(Request $r, $id){
        $requestData = $r->all();
        if(!(Product::find($id))){return response()->json(['message'=>'Produto não existe', 404]);}
        $product = Product::find($id)->update($requestData);
        return response()->json($product);
    }

    public function delete(Request $r, $id){
        Product::find($id)->delete();
        return response()->json(['message'=> 'Produto apagado com sucesso', 200]);
    }
}
