<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'custom_logo',
        'custom_icon',
        'seller_id',
    ];
}
