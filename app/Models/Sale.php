<?php

namespace App\Models;

use App\Models\Product;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use Spatie\Activitylog\Models\Concerns\LogsActivity;
// use Spatie\Activitylog\Support\LogOptions;

class Sale extends Model
{
    // use LogsActivity;

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
        'created_at' => 'date',
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

    public function getTotalPriceAttribute()
    {
        $totalPrice = 0;
        foreach($this->products as $product) {
            $totalPrice += $product->price * $product->pivot->quantity;
        }
        return $totalPrice;
    }

    public function getStatusAfterPaymentAttribute()
    {
        $totalPayments = $this->payments()->sum('amount');
        $totalPrice = $this->total_price;
        if ($totalPayments == 0) {
            return 'unpaid';
        } elseif ($totalPayments > 0 && $totalPayments < $totalPrice) {
            return 'partially_paid';
        } elseif ($totalPayments > 0 && $totalPayments == $totalPrice) {
            return 'paid';
        }

    }

    public function getDueLabelAttribute()
    {
        $today = Carbon::now()->startOfDay();
        $dueDate = Carbon::parse($this->due_date)->startOfDay();

        $daysUntilDue = $today->diffInDays($dueDate, false);

        $isUnpaid = $this->status->label() !== 'paid';
    
        $isOverdue = $isUnpaid && $daysUntilDue < 0;
        $isDueSoon = $isUnpaid && $daysUntilDue >= 0 && $daysUntilDue <= 3;
        $isNotDueYet = $isUnpaid && $daysUntilDue > 3;

        if ($isOverdue) {
            return '<span class="badge bg-danger font-size-12">
                        <i class="bx bx-error-circle me-1"></i>Overdue
                    </span>';
        } elseif ($isDueSoon) {
            return '<span class="badge bg-warning font-size-12">
                <i class="bx bx-time-five me-1"></i>Due in '. $daysUntilDue .' days
            </span>';
        } elseif ($isNotDueYet) {
            return '<span class="badge bg-info font-size-12">
                Not Yet Due
            </span>';
        } else {
            return '<span class="badge bg-'. $this->status->color().' font-size-12">'.
                        ucwords($this->status->label())
                    .'</span>';
        }

    }

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->logOnly(['customer_id', 'user_id', 'status', 'due_date', 'total_amount'])
    //         ->logOnlyDirty();
    // }
}
