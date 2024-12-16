<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;

use Illuminate\Http\Request;

class OrdersController extends Controller
{
    //Aqui é onde vai ficar toda a logica de pedido.
    public function insert(Request $r)
{

    $validated = $r->validate([
        "delivery_address" => "required|string|max:255",
        "products" => "required|array", // Lista de produtos
        "products.*.id" => "required|integer|exists:products,id",
        "products.*.quantity" => "required|integer|min:1",
    ]);

    // Validar estoque dos produtos
    $products = $validated['products'];
    $totalPrice = 0;

    foreach ($products as $product) {
        $productDetails = Product::find($product['id']);
        // Verificação de produto não encontrado
        if (!$productDetails) {
            return response()->json([
                'message' => 'Produto não encontrado com ID: ' . $product['id'],
                'available_stock' => 0,
            ], 400);
        }

        // Verificação de estoque insuficiente
        if ($productDetails->stock < $product['quantity']) {
            return response()->json([
                'message' => 'Estoque insuficiente para o produto ID: ' . $product['id'] . ' Stock: ' . $productDetails->stock,
                'available_stock' => $productDetails->stock,
            ], 400);
        }

        // Calcular preço total
        $totalPrice += $productDetails->price * $product['quantity'];
    }
    try {
        // Criar o pedido
        $order = Order::create([
            'delivery_address' => $validated['delivery_address'],
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        // Adicionar produtos ao pedido
        foreach ($products as $product) {
            $productDetails = Product::find($product['id']);

            // Inserir na tabela intermediária
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $product['id'],
                'quantity' => $product['quantity'],
            ]);

            // Atualizar o estoque do produto
            $productDetails->decrement('stock', $product['quantity']);
        }

        return response()->json(['message' => 'Pedido criado com sucesso', 'order_id' => $order->id], 201);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Erro ao criar pedido: ' . $e->getMessage()], 500);
    }
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
