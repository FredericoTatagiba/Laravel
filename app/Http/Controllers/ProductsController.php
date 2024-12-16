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
            "stock"=> "required|integer|min:0",
            "price"=> "required|numeric|min:0.01",

        ]);
        Product::create($validate);
        return response()->json(['message'=>'Produto criado com sucesso'],200);
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
        $validated = $r->validate([
            "name"=> "nullable|string|max:255",
            "stock"=> "nullable|integer|min:0",
            "price"=> "nullable|numeric|min:0.01",
        ]);
        $product = Product::find($id);
        if(!$product){return response()->json(['message'=>'Produto não existe', 404]);}
        $product->update($validated);
        return response()->json($product);
    }

    public function delete(Request $r, $id){
        Product::find($id)->delete();
        return response()->json(['message'=> 'Produto apagado com sucesso', 200]);
    }
}
