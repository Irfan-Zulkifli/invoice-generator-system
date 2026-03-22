<?php

namespace App\Models;

use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'customer_id',
        'user_id',
        'total_amount',
        'status',
        'due_date',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'due_date' => 'date',
        'status' => \App\Enums\SaleStatus::class
    ];

    public function buyer() {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function seller() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'sale_items', 'sale_id', 'product_id')
                    ->using(SaleItem::class)
                    ->withPivot(['quantity'])
                    ->withTimeStamps();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
