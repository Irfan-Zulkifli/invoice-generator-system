<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SaleItem extends Pivot
{
    protected $table = 'sale_items';

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

}
