<?php

namespace App\Rules;

use App\Models\Sale;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PaymentCheck implements ValidationRule
{
    protected $sale;
    protected $action;

    public function __construct(Sale $sale, $action)
    {
        $this->sale = $sale;
        $this->action = $action;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->action == 'update') {
            $totalPayments = $this->sale->payments()
                                ->where('id', )
                                ->sum('amount');
        } else {
            $totalPayments = $this->sale->payments()->sum('amount');
        }
        

        $totalAfterAdd = $totalPayments + $value;

        $totalPrice = $this->sale->total_price;

        // dapatkan total harga products

        if ($totalAfterAdd > $totalPrice) {
            $fail("The payment made exceed the total needed. Total needed is {$totalPrice}. Current payments made is {$totalPayments}");
        }
    }
}
