<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'status' => App\Enums\SaleStatus::class
    ];

    public function buyer() {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function seller() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
