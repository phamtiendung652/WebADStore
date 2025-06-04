<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WareHouseDetail extends Model
{
    use HasFactory;
    protected $table = 'warehousedetails';

    protected $fillable = [
        'whd_warehouse_id',
        'whd_product_id',
        'whd_qty',
        'whd_time',
    ];
}
