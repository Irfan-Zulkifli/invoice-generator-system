<?php

namespace App\Rules;

use App\Models\Payment;
use App\Models\Sale;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PaymentCheck implements ValidationRule
{
    protected $sale;
    protected $action;
    protected $payment;

    public function __construct(Sale $sale, $action, ?Payment $payment = null)
    {
        $this->sale = $sale;
        $this->action = $action;
        $this->payment = $payment;
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
                ->where('id', '!=', $this->payment->id)
                ->sum('amount');
        } else {
            $totalPayments = $this->sale->payments()->sum('amount');
        }

        $totalAfterAdd = $totalPayments + $value;

        $totalPrice = $this->sale->total_price;

        // dapatkan total harga products

        if ($totalAfterAdd > $totalPrice) {
            $formattedNeeded = number_format($totalPrice, 2);
            $formattedCurrent = number_format($totalPayments, 2);
            $fail("The payment made exceeds the total needed. Total needed is RM {$formattedNeeded}. Current payments made are RM {$formattedCurrent}.");        }
    }
}
