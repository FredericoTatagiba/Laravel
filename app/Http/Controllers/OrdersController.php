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
        "discount"=> "nullable|float|min:0",
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
        if($product->discount || $product->discount > 0) {
            $totalPrice -= ($productDetails->discount * $totalPrice);
        }
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
        $validated = $r->validate([
            "delivery_address" => "nullable|string|max:255",
            "discount"=> "nullable|float|min:0",
            "products" => "nullable|array", // Lista de produtos
            "products.*.id" => "nullable|integer|exists:products,id",
            "products.*.quantity" => "nullable|integer|min:1",
        ]);

        $order = Order::find($id);
        if(!$order){return response()->json(['message'=>'Pedido não existe', 404]);}
        if (isset($validated['products'])) {
            $products = $validated['products'];
            $totalPrice = 0;
            // Verificar se os produtos possuem estoque suficiente
            foreach ($products as $product) {
                $productDetails = Product::find($product['id']);
                if ($productDetails && $productDetails->stock < $product['quantity']) {
                    return response()->json([
                        'message' => 'Estoque insuficiente para o produto ID: ' . $product['id'],
                        'available_stock' => $productDetails->stock,
                    ], 400);
                }
                // Calcular o preço total (somar ao valor já existente)
                $totalPrice += $productDetails->price * $product['quantity'];
            }

            // Atualizar a tabela intermediária `order_products`
            // Remover os produtos antigos
            $order->products()->detach();

            // Adicionar os novos produtos ao pedido
            foreach ($products as $product) {
                $order->products()->attach($product['id'], ['quantity' => $product['quantity']]);
            }
        }

        if($validated['discount'] && $validated['discount'] > 0){
            $totalPrice -= ($totalPrice* $validated['discount']);
        }

        // Atualizar o preço total do pedido
        $validated['total_price'] = $totalPrice;

        $order->update($validated);
        return response()->json($order);
    }
    public function delete(Request $r, $id){

        //Conferir se existe o pedido que queremos deletar
        if(!Order::find($id)){
            return response()->json(['message'=> 'Pedido nao existe.',404]);
        }

        $orderProducts = OrderProduct::where('order_id', $id)->get();

        // Retornar o estoque de cada item para o estoque do produto
        foreach ($orderProducts as $orderProduct) {
            $product = Product::find($orderProduct->product_id);

            // Atualizar o estoque do produto
            if ($product) {
                $product->stock += $orderProduct->quantity;
                $product->save();
            }
        }
        //Após conferido
        //temos de deletar todos os itens adiconado a tabela order_products
        //que tenham o id do pedido a ser excluido/cancelado.
        OrderProduct::where('order_id', $id)->delete();

        //Após feita a exclusão, podemos deletar o pedido que desejamos.
        Order::find($id)->delete();

        return response()->json(['message'=> 'Pedido cancelado com sucesso'], 200);
    }

    public function paid(Request $r, $id){

        $order = Order::find($id);
        if(!$order){return response()->json(['message'=> 'Pedido nao existe.',404]);}
        if($order->status == Order::STATUS_PAID){return response()->json(['message'=> 'Pedido já pago.',304]);}
        $order->setStatusPaid();
        return response()->json(['message'=> 'Pedido pago com sucesso.',200]);

    }
}
