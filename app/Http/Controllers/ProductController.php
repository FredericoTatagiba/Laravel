<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFormRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(ProductFormRequest $request){
        try{
            $product = Product::create($request->all());
            return response()->json(['message'=>'Produto criado com sucesso'],200);
        }catch(\Exception $e){
            return response()->json(['message'=> 'Falha ao criar produto'],500);
        }
    }

    public function read(Request $request, $id){
        $product = Product::find($id);
        if(!$product){return response()->json(['message'=>'Produto não encontrado', 404]);}
        return response()->json($product);
    }

    public function all(Request $request){
        //colocar opções de filtro.
        return Product::all();
    }

    public function update(ProductFormRequest $request, $id){
        try{
            $product = Product::find($id);
            if(!$product) {
                return response()->json(['message'=>'Produto não existe', 404]);
            }
            $product->update($request->all());
            return response()->json($product);

        } catch(\Exception $e) {
            return response()->json(['message'=> 'Falha ao atualizar produto'],500);
        }    
    }

    public function destroy(Request $request, $id){
        Product::find($id)->delete();
        return response()->json(['message'=> 'Produto apagado com sucesso', 200]);
    }
}
