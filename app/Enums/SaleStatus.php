<?php

enum SaleStatus: string {
    case PAID = 'paid';
    case UNPAID = 'unpaid';
    case PARTIALLY_PAID = 'partially_paid';

    public function color(): string {
        return match($this) {
            self::PAID =>'success',
            self::UNPAID => 'danger',
            self::PARTIALLY_PAID => 'warning',
        };
    }

    public function label(): string {
        return match($this) {
            self::PAID => 'paid',
            self::UNPAID => 'unpaid',
            self::PARTIALLY_PAID => 'partially paid'
        };
    }
}