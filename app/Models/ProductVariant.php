<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;
    protected $table = 'product_variants'; // Tên bảng của bạn
    protected $fillable = [
        'product_id',
        'variant_name',
        'color',
        'sku',
        'price_adjustment',
        'current_price',
        'quantity_in_stock',
        'variant_image',
        'is_active',
    ];

    /**
     * Get the product that owns the variant.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
