<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class product extends Model
{
  use HasSlug;

    protected $fillable = ['name', 'slug', 'description', 'price', 'stock_quantity', 'category_id'];

    public function getSlugOptions() : SlugOptions {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function category() {
        return $this->belongsTo(Categorie::class);
    }

    public function images() {
        return $this->hasMany(ProductImage::class);
    }

    public function orders() {
        return $this->belongsToMany(Order::class, 'order_items')->withPivot('quantity', 'price_at_purchase');
    }
}
