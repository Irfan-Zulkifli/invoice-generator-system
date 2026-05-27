<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
// use Spatie\Activitylog\Models\Concerns\LogsActivity;
// use Spatie\Activitylog\Support\LogOptions;

class Customer extends Model
{
    // use LogsActivity;
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

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //             ->logOnly(['name', 'email', 'phone', 'address', 'seller_id'])
    //             ->logOnlyDirty();
    // }

}
