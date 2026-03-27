<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'categorie_id' => 'required|exists:categories,id', 
            'photos' => 'required|array|max:4', 
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048'
        ];
    }
}