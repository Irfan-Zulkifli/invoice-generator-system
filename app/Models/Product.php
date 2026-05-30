<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
// use Spatie\Activitylog\Models\Concerns\LogsActivity;
// use Spatie\Activitylog\Support\LogOptions;

class Product extends Model
{
    // use LogsActivity;

    protected $fillable = [
        'name',
        'description',
        'price',
        'min_stock',
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

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function getCurrentStockAttribute()
    {
        return $this->inventoryMovements()->sum('quantity'); 
    }

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //             ->logOnly(['name', 'description', 'price', 'creator_id'])
    //             ->logOnlyDirty();
    // }
}
