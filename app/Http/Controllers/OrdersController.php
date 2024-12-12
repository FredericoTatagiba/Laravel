<?php

namespace App\Http\Controllers;
use App\Models\Order;

use Illuminate\Http\Request;

class OrdersController extends Controller
{
    //Aqui é onde vai ficar toda a logica de pedido.
    public function insert(Request $r){

    }
    public function read(Request $r, $id){
        $order = Order::find($id);
        if(!$order){return response()->json(['message'=>'Pedido não encontrado', 404]);}
        return response()->json($order);
    }
    public function all(Request $r){
        $order = Order::all();
        return $order;
    }
    public function update(Request $r, $id){

    }
    public function delete(Request $r, $id){
        Order::find($id)->delete();
        return response()->json(['message'=> 'Pedido cancelado com sucesso'], 200);
    }
}
