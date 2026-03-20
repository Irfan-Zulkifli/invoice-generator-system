<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'creator_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function Sale(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
                    ->using(SaleItem::class, 'product_id', 'sale_id')
                    ->withPivot(['quantity', 'unit_price', 'subtotal'])
                    ->withTimeStamps();
    }
}
