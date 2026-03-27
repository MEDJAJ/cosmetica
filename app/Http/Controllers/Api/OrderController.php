<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function store(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
          
            return DB::transaction(function () use ($request) {
                
           
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'total_price' => 0,       
                    'status' => 'en attente',
                ]);

                $totalCommande = 0;

              
                foreach ($request->items as $itemData) {
                    $product = Product::findOrFail($itemData['product_id']);

                    
                    if ($product->stock_quantity < $itemData['quantity']) {
                        throw new \Exception("Stock insuffisant pour le produit : " . $product->name);
                    }

                  
                    $prixAuMomentAchat = $product->price;
                    $sousTotal = $prixAuMomentAchat * $itemData['quantity'];
                    $totalCommande += $sousTotal;

                   
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $itemData['quantity'],
                        'price_at_purchase' => $prixAuMomentAchat
                    ]);

                
                    $product->decrement('stock_quantity', $itemData['quantity']);
                }

              
                $order->update(['total_price' => $totalCommande]);

                return response()->json([
                    'message' => 'Commande validée avec succès',
                    'order' => $order->load('items.product')
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la commande',
                'error' => $e->getMessage()
            ], 400);
        }
    }




  
     public function index()
      {
  
      $userId = auth()->id();

    
      $orders = Order::with(['items.product'])
        ->where('user_id', $userId)
        ->latest()
        ->get();

     if ($orders->isEmpty()) {
        return response()->json([
            'message' => "Vous n'avez pas encore passé de commande."
        ], 200);
     }

     return response()->json([
        'message' => 'Historique des commandes récupéré avec succès',
        'orders' => $orders
     ], 200);
     }


   
public function cancel($id)
{
    
    $order = Order::with('items.product')
        ->where('id', $id)
        ->where('user_id', auth()->id())
        ->first();

   
    if (!$order) {
        return response()->json([
            'message' => "Commande non trouvée ou vous n'avez pas l'autorisation."
        ], 404);
    }

   
    if ($order->status !== 'en attente') {
        return response()->json([
            'message' => "Impossible d'annuler une commande déjà {$order->status}."
        ], 400);
    }

    try {
        return DB::transaction(function () use ($order) {
            
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock_quantity', $item->quantity);
                }
            }

            
            $order->update(['status' => 'annulée']);

            return response()->json([
                'message' => 'Votre commande a été annulée avec succès et le stock a été mis à jour.',
                'order' => $order
            ], 200);
        });
    } catch (\Exception $e) {
        return response()->json([
            'message' => "Erreur lors de l'annulation.",
            'error' => $e->getMessage()
        ], 500);
    }
}



public function updateStatus(Request $request, $id)
{
    $order = Order::find($id);

    if (!$order) {
        return response()->json(['message' => 'Commande non trouvée'], 404);
    }

   
    $validator = Validator::make($request->all(), [
        'status' => 'required|in:en attente,en préparation,livrée,annulée'
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $order->update(['status' => $request->status]);

    return response()->json([
        'message' => "Le statut de la commande #{$id} est désormais : {$request->status}",
        'order' => $order
    ]);
}




public function markAsPrepared($id)
{
    
    $order = Order::find($id);

   
    if (!$order) {
        return response()->json([
            'status' => 'Erreur',
            'message' => 'Commande introuvable.'
        ], 404);
    }


    if ($order->status !== 'en attente') {
        
       
        if ($order->status === 'annulée') {
            return response()->json([
                'status' => 'Action refusée',
                'message' => 'Impossible de préparer une commande qui a été annulée.'
            ], 400);
        }

        
        return response()->json([
            'status' => 'Action inutile',
            'message' => "La commande est déjà au statut : {$order->status}."
        ], 400);
    }

    
    $order->update(['status' => 'en préparation']);

    return response()->json([
        'status' => 'Succès',
        'message' => "La commande #{$id} est maintenant passée en préparation.",
        'order' => $order
    ], 200);
}
}