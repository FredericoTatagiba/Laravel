<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFormRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function register(ProductFormRequest $request){
        try{
            $product = Product::create($request->all());
            return response()->json(['message'=>'Produto criado com sucesso', 'product' => $product],200);
        }catch(\Exception $e){
            return response()->json(['message'=> 'Falha ao criar produto'],500);
        }
    }

    public function read($id){
        $product = Product::find($id);
        if(!$product) {
            return response()->json(['message'=>'Produto não encontrado', 404]);
        }
        return response()->json($product);
    }

    public function all($filter){
        if($filter){
            $product = Product::where(column: $filter)->get();
            return $product;
        }
        $product = Product::all();
        return $product;
    }

    public function update(ProductUpdateRequest $request, $id){
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

    public function delete(Request $request, $id){
        $product = Product::find($id)->delete();
        return response()->json(['message'=> 'Produto apagado com sucesso', 'product' => $product], 200);
    }
}
