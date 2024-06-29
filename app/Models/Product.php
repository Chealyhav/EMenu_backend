<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'productCode',
        'image',
        'price',
        'des',
    ];

    /**
     * Get the category that owns the product.
     */
    // public function menutag(): BelongsToMany
    // {
    //     return $this->belongsToMany(MenuTag::class);
    // }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id');
    }
}
