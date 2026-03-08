<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'seller_id'
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

}
