<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specification extends Model
{
    use HasFactory;

    protected $table = 'specifications'; // Tên bảng của bạn
    protected $fillable = [
        'sp_product_id',
        'sp_cpu',
        'sp_gpu',
        'sp_ram',
        'sp_storage',
        'sp_display',
    ];

    // Define relationship with Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'sp_product_id');
    }
}