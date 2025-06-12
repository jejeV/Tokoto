<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'image',
    ];

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getDefaultPriceAttribute()
    {
        return $this->productVariants->sortBy('price')->first()->price ?? $this->price;
    }

    public function getDefaultImageAttribute()
    {
        return $this->productVariants->first()->image ?? $this->image;
    }
}
