<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\order;
use App\Models\orderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Statistique extends Controller
{
    public function getStatistics()
{
   
    $deliveredOrdersCount = order::where('status', 'livrée')->count();

    
    $totalAmount = Order::where('status', '!=', 'annulée')->sum('total_price');

   
    $categoriesCount = Categorie::count();

  
    $productsPerCategory = Categorie::withCount('products')->get();

   
    $popularProducts = orderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
        ->with('product:id,name,price') 
        ->groupBy('product_id')
        ->orderByDesc('total_sold')
        ->take(3)
        ->get();

    return response()->json([
        'status' => 'Succès',
        'data' => [
            'orders_delivered' => $deliveredOrdersCount,
            'total_revenue' => $totalAmount,
            'total_categories' => $categoriesCount,
            'inventory_split' => $productsPerCategory,
            'top_products' => $popularProducts
        ]
    ], 200);
}
}
