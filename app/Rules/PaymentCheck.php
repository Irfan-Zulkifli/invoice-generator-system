<?php

namespace App\Rules;

use App\Models\Sale;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PaymentCheck implements ValidationRule
{
    protected $sale;

    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $totalPayments = $this->sale->payments()->sum('amount');

        $totalAfterAdd = $totalPayments + $value;

        $totalPrice = $this->sale->total_price;

        // dapatkan total harga products

        if ($totalAfterAdd > $totalPrice) {
            $fail("The payment made exceed the total needed. Total needed is {$totalPrice}. Current payments made is {$totalPayments}");
        }
    }
}
