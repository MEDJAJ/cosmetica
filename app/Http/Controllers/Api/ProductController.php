<?php

namespace App\Http\Controllers\Api;

use App\DTOs\ProductDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Models\ProductImage;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $productRepo;

    public function __construct(ProductRepository $productRepo) {
        $this->productRepo = $productRepo;
    }
   public function store(StoreProductRequest $request)
    {
        
        $dto = ProductDTO::fromRequest($request);

        
        $product = DB::transaction(function () use ($dto) {
            
          
            $product = Product::create([
                'name' => $dto->name,
                'description' => $dto->description,
                'price' => $dto->price,
                'stock_quantity' => $dto->stock_quantity,
                'categorie_id' => $dto->categorie_id,
            ]);

           
            foreach ($dto->photos as $file) {
                $path = $file->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id, 
                    'path' => $path
                ]);
            }

            return $product;
        });

        return response()->json([
            'message' => 'Produit et images enregistrés avec succès via DTO',
            'product' => $product->load('images')
        ], 201);
    }



   
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product){
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'string',
            'price' => 'numeric',
            'stock_quantity' => 'integer',
            'category_id' => 'exists:categories,id',
            'photos' => 'array|max:4', 
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

      
        $product->update($request->only(['name', 'description', 'price', 'stock_quantity', 'category_id']));

        
        if ($request->hasFile('photos')) {
            
           
            foreach ($product->images as $oldImage) {
                Storage::disk('public')->delete($oldImage->path);
                $oldImage->delete();
            }

      
            foreach ($request->file('photos') as $file) {
                $path = $file->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path
                ]);
            }
        }

        return response()->json([
            'message' => 'Produit mis à jour avec succès',
            'product' => $product->load('images')
        ]);
    }

    
    public function destroy($id)
    {
     
        $product = Product::with('images')->find($id);

        if (!$product) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        }

      
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
           
        }

        $product->delete();

        return response()->json([
            'message' => 'Produit et toutes ses images supprimés avec succès'
        ]);
    }

  
    public function index()
    {
    
        $products = $this->productRepo->getAllProducts();

        if ($products->isEmpty()) {
            return response()->json([
                'message' => 'Aucun produit trouvé'
            ], 404);
        }

        return response()->json([
            'message' => 'Liste des produits récupérée avec succès',
            'data' => $products
        ], 200);
    }



public function show($slug)
{
   
    $product = $this->productRepo->getProductBySlug($slug);


    if (!$product) {
        return response()->json([
            'status' => 'Erreur',
            'message' => "Désolé, le produit avec le slug '$slug' n'existe pas dans notre catalogue."
        ], 404);
    }

    
    return response()->json([
        'status' => 'Succès',
        'message' => 'Détails du produit récupérés avec succès',
        'product' => $product
    ], 200);
}
}