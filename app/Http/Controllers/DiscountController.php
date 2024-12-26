<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiscountFormRequest;
use App\Http\Requests\DiscountUpdateRequest;
use Illuminate\Http\Request;
use App\Models\Discount;

class DiscountController extends Controller
{
    //Criar o CRUD para descontos. Pode ter delete.
    
    public function store(DiscountFormRequest $request){
        try{
            $discount = Discount::create($request->all());
            return response()->json(['message'=>'Desconto criado com sucesso', 'discount'=>$discount],200);
        }catch(\Exception $e){
            return response()->json(['message'=> 'Falha ao criar desconto'],500);
        }
    }

    public function show($id){

        try{
            $discount = Discount::find($id);
            if(!$discount) {
                return response()->json(['message'=>'Desconto não encontrado', 404]);
            }
            return response()->json($discount, 200);

        } catch(\Exception $e) {
            return response()->json(['message'=> 'Falha ao buscar desconto'],500);
        }
    }

    public function update(DiscountUpdateRequest $request, $id){
        try{
            $discount = Discount::find($id);
            if(!$discount) {
                return response()->json(['message'=>'Desconto não existe', 404]);
            }
            $discount->update($request->all());
            return response()->json($discount);

        } catch(\Exception $e) {
            return response()->json(['message'=> 'Falha ao atualizar desconto'],500);
        }    
    }

    public function delete($id){
        try{
            $discount = Discount::find($id);
            if(!$discount) {
                return response()->json(['message'=>'Desconto não existe', 404]);
            }
            $discount->delete();
            return response()->json(['message'=> 'Desconto apagado com sucesso', 'discount'=>$discount],200);

        } catch(\Exception $e) {
            return response()->json(['message'=> 'Falha ao apagar desconto'],500);
        }

    }
}
