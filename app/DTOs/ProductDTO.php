<?php
namespace App\DTOs;

use App\Http\Requests\StoreProductRequest;
use Illuminate\Support\Str;

class ProductDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly float $price,
        public readonly int $stock_quantity,
        public readonly int $categorie_id,
        public readonly array $photos 
    ) {}

    public static function fromRequest(StoreProductRequest $request): self
    {
        
        return new self(
            name: $request->validated('name'),
            description: $request->validated('description'),
            price: (float) $request->validated('price'),
            stock_quantity: (int) $request->validated('stock_quantity'),
            categorie_id: (int) $request->validated('categorie_id'),
           
            photos: $request->file('photos') ?? [] 
        );
    }
}