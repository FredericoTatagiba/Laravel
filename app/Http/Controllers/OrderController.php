<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderFormRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Discount;
use App\Models\Client;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    //Aqui é onde vai ficar toda a logica de pedido.
    public function store(Request $request)
    {
        
        // Verificar se o cliente existe na tabela de clients
        

        // Validar estoque dos produtos
        $products = $request['products'];
        $totalPrice = 0;

        // Obter todos os produtos de uma vez, para evitar múltiplas consultas
        $productIds = array_column($products, 'id');
        $productDetails = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($products as $product) {
            if (!isset($productDetails[$product['id']])) {
                return response()->json([
                    'message' => 'Produto não encontrado com ID: ' . $product['id'],
                    'available_stock' => 0,
                ], 400);
            }

            $productDetail = $productDetails[$product['id']];

            // Verificação de estoque insuficiente
            if ($productDetail->stock < $product['quantity']) {
                return response()->json([
                    'message' => 'Estoque insuficiente para o produto ID: ' . $product['id'] . '. Stock: ' . $productDetail->stock,
                    'available_stock' => $productDetail->stock,
                ], 400);
            }

            // Calcular preço total
            $totalPrice += $productDetail->price * $product['quantity'];
        }

        // Acessar tabela desconto e pegar o valor para aplicar.
        $discount = Discount::orderBy('price', 'desc')
            ->where('price', '<', $totalPrice)->first();

        if ($discount) {
            $totalPrice -= ($totalPrice * $discount->discount) / 100;
        }

        try {
            // Criar o pedido
            $order = Order::create([
                'client_id' => $request->client_id, // Aqui é o cliente do sistema, e não o usuário autenticado
                'delivery_address' => $request->delivery_address,
                'total_price' => round($totalPrice,2),
                'discount' => $discount ? $discount->discount : 0,
                'status' => Order::STATUS_PENDING,
            ]);

            $orderProducts = [];
            // Adicionar produtos ao pedido
            foreach ($products as $product) {
                $productDetail = $productDetails[$product['id']];

                // Inserir na tabela intermediária
                $orderProduct = OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'unity_price' => $productDetail->price,
                ]);

                // Atualizar o estoque do produto
                $productDetail->decrement('stock', $product['quantity']);

                //Apenas para visualização
                $orderProducts[] = [
                    'id' => $orderProduct->id,
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'unity_price' => $productDetail->price,
                ];
            }

            return response()->json(['message' => 'Pedido criado com sucesso', 'order' => $order, 'order_products' => $orderProducts], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao criar pedido: ' . $e->getMessage()], 500);
        }
    }



    public function read($id){
        $order = Order::find($id);
        if(!$order){return response()->json(['message'=>'Pedido não encontrado', 404]);}
        return response()->json($order);
    }
    public function all(){
        $order = Order::all();
        return $order;
    }

    //Necessita de revisão.
    public function update(OrderUpdateRequest $request, $id){

        $totalPrice = 0;
        $order = Order::find($id);

        //Verifica se o pedido existe.
        if(!$order) {
            return response()->json(['message'=>'Pedido não existe',], 404);
        }

        //Não há necessidade de verficar se existe produtos, pois se o pedido existe ele vai ter produtos
        //por causa da validação do formrequest no momento de ser criado.
        $products = $request['products'];

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

        //Acessar tabela desconto e pega o valor para aplicar.
        $discount = Discount::orderBy('price', 'desc')
            ->where('price', '<', $totalPrice)->first();
        if(!$discount) {
            $discount = 0;
        } else {
            $totalPrice -= ($totalPrice * $discount->discount)/100;
        }

        // Atualizar o preço total do pedido
        $request['total_price'] = $totalPrice;

        $order->update($request);
        return response()->json($order);
    }

    public function cancel(Request $request, $id){

        $order = Order::find($id);
        
        //Verifica se o pedido existe.
        if(!$order) {
            return response()->json(['message'=> 'Pedido nao existe.',404]);
        }
        
        //Verifica se o status do pedido pode ser alterado.
        if($order->status != Order::STATUS_PENDING) {
            return response()->json(['message'=> 'Pedido já pago/cancelado.',304]);
        }

        //Altera o status do pedido para cancelado.
        $order->setStatusCanceled();
        return response()->json(['message'=> 'Pedido cancelado com sucesso.',200]);
    }

    public function paid(Request $request, $id){

        $order = Order::find($id);

        //Verifica se o pedido existe.
        if(!$order) {
            return response()->json(['message'=> 'Pedido nao existe.',404]);
        }
        
        //Verifica se o status do pedido pode ser alterado.
        if($order->status != Order::STATUS_PENDING) {
            return response()->json(['message'=> 'Pedido já pago/cancelado.',304]);
        }

        //Altera o status do pedido para pago.
        $order->setStatusPaid();
        return response()->json(['message'=> 'Pedido pago com sucesso.',200]);
    }
}
