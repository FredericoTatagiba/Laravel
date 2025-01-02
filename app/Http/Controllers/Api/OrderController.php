<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        // Validar a existência do cliente
        $client = Client::find($request->client_id);
        if (!$client) {
            return response()->json(['message' => 'Cliente não encontrado.'], 404);
        }

        // Validar dados de entrada
        $validated = $request->validate([
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id', // Verificar se o produto existe
            'products.*.quantity' => 'required|integer|min:1', // Verificar se a quantidade é positiva
            'delivery_address' => 'required|string|max:255',
        ]);

        // Obter os produtos do pedido
        $products = $validated['products'];
        $totalPrice = 0;

        // Obter todos os produtos de uma vez, para evitar múltiplas consultas
        $productIds = array_column($products, 'id');
        $productDetails = Product::whereIn('id', $productIds)->get()->keyBy('id');

        // Verificar cada produto
        foreach ($products as $product) {
            $productDetail = $productDetails->get($product['id']);
            
            if (!$productDetail) {
                return response()->json([
                    'message' => 'Produto não encontrado com ID: ' . $product['id'],
                    'available_stock' => 0,
                ], 400);
            }

            // Verificação de estoque insuficiente
            if ($productDetail->stock < $product['quantity']) {
                return response()->json([
                    'message' => 'Estoque insuficiente para o produto ID: ' . $product['id'] . '. Estoque disponível: ' . $productDetail->stock,
                    'available_stock' => $productDetail->stock,
                ], 400);
            }

            // Calcular o preço total
            $totalPrice += $productDetail->price * $product['quantity'];
        }

        // Acessar tabela de desconto e aplicar
        $discount = Discount::orderBy('price', 'desc')
            ->where('price', '<', $totalPrice)
            ->first();

        if ($discount) {
            $totalPrice -= ($totalPrice * $discount->discount) / 100;
        }

        try {
            // Criar o pedido
            $order = Order::create([
                'client_id' => $client->id,
                'delivery_address' => $request->delivery_address,
                'total_price' => round($totalPrice, 2),
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

                // Apenas para visualização
                $orderProducts[] = [
                    'id' => $orderProduct->id,
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'unity_price' => $productDetail->price,
                ];
            }

            return response()->json([
                'message' => 'Pedido criado com sucesso.',
                'order' => $order,
                'order_products' => $orderProducts,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar pedido: ' . $e->getMessage(),
            ], 500);
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
    public function update(Request $request, $id)
    {
        try {
            // Encontrar o pedido
            $order = Order::find($id);
            if (!$order) {
                return response()->json(['message' => 'Pedido não encontrado.'], 404);
            }

            // Validar dados de entrada
            $validated = $request->validate([
                'products' => 'required|array|min:1',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
            ]);

            $products = $validated['products'];
            $totalPrice = 0;

            // Obter todos os produtos de uma vez, para evitar múltiplas consultas
            $productIds = array_column($products, 'id');
            $productDetails = Product::whereIn('id', $productIds)->get()->keyBy('id');

            // Validar estoque e calcular o preço total
            foreach ($products as $product) {
                $productDetail = $productDetails->get($product['id']);

                if (!$productDetail) {
                    return response()->json([
                        'message' => 'Produto não encontrado com ID: ' . $product['id'],
                    ], 400);
                }

                // Verificar se o estoque é suficiente
                if ($productDetail->stock < $product['quantity']) {
                    return response()->json([
                        'message' => 'Estoque insuficiente para o produto ID: ' . $product['id'],
                        'available_stock' => $productDetail->stock,
                    ], 400);
                }

                // Calcular o preço total
                $totalPrice += $productDetail->price * $product['quantity'];
            }

            // Acessar a tabela de desconto e aplicar
            $discount = Discount::orderBy('price', 'desc')
                ->where('price', '<', $totalPrice)
                ->first();

            if ($discount) {
                $totalPrice -= ($totalPrice * $discount->discount) / 100;
            }

            // Atualizar a tabela intermediária `order_products`
            try {
                // Remover os produtos antigos
                $order->products()->detach();

                // Associar os novos produtos ao pedido
                foreach ($products as $product) {
                    $order->products()->attach($product['id'], [
                        'quantity' => $product['quantity'],
                        'unity_price' => $productDetails[$product['id']]->price,
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Erro ao atualizar os produtos do pedido: ' . $e->getMessage(),
                ], 500);
            }

            // Atualizar o pedido com o novo total
            try {
                $order->update([
                    'total_price' => round($totalPrice, 2),
                    'discount' => $discount ? $discount->discount : 0,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Erro ao atualizar o pedido: ' . $e->getMessage(),
                ], 500);
            }

            return response()->json([
                'message' => 'Pedido atualizado com sucesso.',
                'order' => $order->load('products'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao processar a atualização do pedido: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function cancel($id){

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

    public function paid($id){

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
