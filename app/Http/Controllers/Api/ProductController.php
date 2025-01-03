<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFormRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(ProductFormRequest $request){
        try{
            $product = Product::create($request->all());
            return response()->json([
                'message'=>'Produto criado com sucesso',
                'product' => $product,
            ],200);
        }catch(\Exception $e){
            return response()->json(['message'=> 'Falha ao criar produto'],500);
        }
    }

    public function show($id){
        try {
            $product = Product::find($id);
            if(!$product) {
                return response()->json(['message'=>'Produto não encontrado'], 404);
            }
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['message'=> 'Falha ao buscar produto'],500);
        }
    }

    public function index(Request $request){
        try {
            if($request->has('name')){
                $products = Product::where('name', 'like', '%' . $request->name . '%')
                                ->paginate(5);
                return response()->json($products);
            }

            if($request->has('price')){
                $products = Product::where('price', 'like', '%' . $request->price . '%')
                                ->paginate(5);
                return response()->json($products);
            }

            $product = Product::paginate(5);
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['message'=> 'Falha ao listar produtos'],500);
        }
    }

    public function update(ProductUpdateRequest $request, $id){
        try{
            $product = Product::find($id);
            if(!$product) {
                return response()->json(['message'=>'Produto não existe'], 404);
            }
            $product->update($request->all());
            return response()->json([
                'message'=> 'Produto atualizado com sucesso',
                'product' => $product], 200);

        } catch(\Exception $e) {
            return response()->json(['message'=> 'Falha ao atualizar produto'],500);
        }    
    }

    public function delete($id){
        try {
            $product = Product::find($id);
            if(!$product) {
                return response()->json(['message'=>'Produto não encontrado'], 404);
            }
            Product::destroy($id);
            return response()->json([
                'message'=> 'Produto apagado com sucesso', 
                'product' => $product
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message'=> 'Falha ao apagar produto'],500);
        }
    }
}
