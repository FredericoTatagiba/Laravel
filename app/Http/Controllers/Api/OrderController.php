<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Discount;
use App\Models\Client;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    //Aqui é onde vai ficar toda a logica de pedido.
    
    public function index(Request $request){
        try {
            if($request->has("protocol")){
                $orders = Order::where('protocol', 'like', '%' . $request->protocol . '%')
                                ->paginate(5);
                return response()->json($orders);
            }
            $orders = Order::paginate(5);
            return response()->json($orders);
        } catch (\Exception $e) {
            return response()->json(['message'=> 'Falha ao listar pedidos'],500);
        }
    }

    public function show($id){
        try {
            $order = Order::find($id);

            if(!$order) {
                return response()->json(['message'=>'Pedido não encontrado'], 404);
            }

            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['message'=> 'Falha ao buscar pedido'],500);
        }
    }

    public function destroy($id){
        try {
            $order = Order::find($id);
            if(!$order) {
                return response()->json(['message'=>'Pedido não encontrado'], 404);
            }
            $order->delete();
            return response()->json(['message'=>'Pedido deletado com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['message'=> 'Falha ao deletar pedido'],500);
        }
    }

    public function store(Request $request){
        try {
            // Validar a existência do cliente
            $client = Client::find($request->client_id);
            if(!$client) {
                return response()->json(['message'=>'Cliente não encontrado'], 404);
            }

            // Validar dados de entrada
            $validated = $request->validate([
                'products' => 'required|array|min:1',
                'products.*.id' => 'required|exists:products,id', // Verificar se o produto existe
                'products.*.quantity' => 'required|integer|min:1', // Verificar se a quantidade é positiva
                'delivery_address' => 'required|string|max:255',
                'payment_method' => 'required|integer|in:10,11,12',
            ]);

            // Obter os produtos do pedido
            $products = $validated['products'];
            $totalPrice = 0;

            // Obter todos os produtos de uma vez, para evitar múltiplas consultas
            $productIds = array_column($products, 'id');
            $productDetails = Product::whereIn('id', $productIds)
                                        ->get()->keyBy('id');

            // Verificar cada produto
            foreach ($products as $product) {
                $productDetail = $productDetails->get($product['id']);
                
                // Verificar se o produto existe
                if (!$productDetail) {
                    return response()->json([
                        'message' => 'Produto com ID ('. $product['id'].') não encontrado.',
                    ], 404);
                }

                // Verificar se há estoque suficiente
                if ($productDetail->stock < $product['quantity']) {
                    return response()->json([
                        'message' => 'Produto ('. $productDetail->name .') sem estoque suficiente.',
                        'stock' => 'Estoque disponivel:'.$productDetail->stock,
                    ], 400);
                }

                // Calcular o preço total
                $totalPrice += $productDetail->price;
            }

            // Acessar tabela de desconto e aplicar
            $discount = Discount::orderBy('price', 'desc')
            ->where('price', '<', $totalPrice)
            ->first();
            
            if ($discount) {
                $totalPrice -= ($totalPrice * $discount->discount) / 100;
            }

            try {
                $order = Order::create([
                    'client_id' => $client->id,
                    'protocol' => hexdec(uniqid()),
                    'delivery_address' => $validated['delivery_address'],
                    'total_price' => round($totalPrice, 2),
                    'discount' => $discount ? $discount->discount : 0,
                    'status' => Order::STATUS_PENDING,
                    'payment_method' => $validated['payment_method'],
                ]);

                //Adicionar os produtos ao pedido
                $orderProducts = [];

                foreach ($products as $product) {
                    $productDetail = $productDetails->get($product['id']);

                    $orderProducts[] = new OrderProduct([
                        'order_id' => $order->id,
                        'product_id' => $product['id'],
                        'quantity' => $product['quantity'],
                        'price' => $productDetails->get($product['id'])->price,
                    ]);

                    // Atualizar o estoque
                    $productDetail->decrement('stock', $product['quantity']);

                }

                return response()->json([
                    'message' => 'Pedido criado com sucesso.',
                    'order' => $order,
                    'order_products' => $orderProducts,
                ], 201);
            } catch (\Exception $e) {
                return response()->json(['message'=> 'Falha ao criar pedido'],500);
            }
        } catch (\Exception $e) {
            return response()->json(['message'=> 'Erro ao processar pedido'],500);
        }
    }

    public function update(Request $request, $id){
        try {
            $order = Order::find($id);
            if (!$order) {
                return response()->json(['message' => 'Pedido não encontrado'], 404);
            }

            $validated = $request->validate([
                'delivery_address' => 'nullable|string|max:255',
                'products' => 'nullable|array|min:1',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required_with:products|integer|min:1',
            ]);

            $currentProducts = $order->products->keyBy('id'); // Produtos atuais no pedido

            if (isset($validated['products'])) {
                $products = $validated['products'];
                $prodIds = array_column($products, 'id');
                $productsDetails = Product::whereIn('id', $prodIds)->get()->keyBy('id');

                // Verificar estoque e processar novos produtos
                foreach ($products as $product) {
                    $prodDetail = $productsDetails->get($product['id']);

                    if (!$prodDetail) {
                        return response()->json(['message' => 'Produto não encontrado'], 404);
                    }

                    if ($prodDetail->stock < $product['quantity']) {
                        return response()->json([
                            'message' => 'Estoque insuficiente do produto de ID: ' . $product['id'],
                            'stock' => 'Estoque disponível: ' . $prodDetail->stock,
                        ], 400);
                    }
                }

                // Reabastecer estoque de produtos removidos
                foreach ($currentProducts as $currentProductId => $currentProduct) {
                    if (!in_array($currentProductId, $prodIds)) {
                        $product = Product::find($currentProductId);
                        $product->stock += $currentProduct->pivot->quantity; // Reabastecer estoque
                        $product->save();
                    }
                }

                // Atualizar o estoque dos produtos adicionados ou modificados
                foreach ($products as $product) {
                    $prodDetail = $productsDetails->get($product['id']);
                    $previousQuantity = $currentProducts->get($product['id'])->pivot->quantity ?? 0;

                    // Ajustar estoque baseado na nova quantidade
                    $prodDetail->stock -= ($product['quantity'] - $previousQuantity);
                    $prodDetail->save();
                }

                // Atualizar os produtos no pedido
                $order->products()->sync(
                    collect($products)->mapWithKeys(fn($product) => [
                        $product['id'] => ['quantity' => $product['quantity']]
                    ])->toArray()
                );
            }

            // Atualizar endereço de entrega, se fornecido
            if (isset($validated['delivery_address'])) {
                $order->delivery_address = $validated['delivery_address'];
            }

            $order->save();

            return response()->json(['message' => 'Pedido atualizado com sucesso', 'order' => $order], 200);
        } catch (\Exception $e) {
            return response()->json(['message'=> 'Erro ao atualizar pedido'],500);
        }
    }

    public function cancel($id)
    {
        try {
            $order = Order::findOrFail($id);
            
            //Verifica se o status do pedido pode ser alterado.
            if ($order->status != Order::STATUS_PENDING) {
                return response()->json(['message' => 'Pedido já pago/cancelado.'], 304);
            }

            // Reabastece o estoque dos produtos associados ao pedido
            foreach ($order->products as $product) {
                $product->stock += $product->pivot->quantity; // Incrementa o estoque com a quantidade do pedido
                $product->save();
            }


            //Altera o status do pedido para cancelado.
            $order->setStatusCanceled();

            return response()->json(['message' => 'Pedido cancelado com sucesso.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao cancelar o pedido: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function paid($id)
    {
        try {
            $order = Order::findOrFail($id);
            
            //Verifica se o status do pedido pode ser alterado.
            if ($order->status != Order::STATUS_PENDING) {
                return response()->json(['message' => 'Pedido já pago/cancelado.'], 304);
            }

            //Altera o status do pedido para pago.
            $order->setStatusPaid();
            return response()->json(['message' => 'Pedido pago com sucesso.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao processar o pagamento do pedido: ' . $e->getMessage(),
            ], 500);
        }
    }
}
