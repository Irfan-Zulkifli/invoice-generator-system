<?php

namespace App\Actions;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class UpdateSaleAction {

    public function execute(Sale $sale, Array $data)
    {
        
        return DB::transaction(function () use ($sale, $data) {

            if ($data['formRadios'] == 'no') {
                $customer = Customer::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'seller_id' => auth()->id(),
                ]);

            } else {
                $customer = Customer::findOrFail($data['customer_id']);
            }

            $sale->update([
                'customer_id' => $customer->id,
                'user_id' => auth()->id(),
                'due_date' => $data['due_date'],
            ]);

            $syncData = [];

            foreach ($data['product_id'] as $index => $productId) {
                $quantity = (int) $data['quantity'][$index];

                if (isset($syncData[$productId])) {
                    $syncData[$productId]['quantity'] += $quantity;
                } else {
                    $syncData[$productId] = [
                        'quantity' => $quantity
                    ];
                }

            }

            $sale->products()->sync($syncData);

            return $sale;

        });
    }
}