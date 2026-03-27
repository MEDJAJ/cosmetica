<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository {
    
   
    public function getAllProducts() {
        return Product::with(['images', 'category'])->get();
    }

   
    public function getProductBySlug($slug) {
        return Product::with(['images', 'category'])
            ->where('slug', $slug)
            ->first();
    }
}