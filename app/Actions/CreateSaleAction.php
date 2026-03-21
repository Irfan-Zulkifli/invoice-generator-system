<?php

namespace App\Actions;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class CreateSaleAction {

    public function execute(Array $data)
    {
        // dd($data['product_id']);
        return DB::transaction(function () use ($data) {

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

            $sale = Sale::create([
                'customer_id' => $customer->id,
                'user_id' => auth()->id(),
                'status' => 'unpaid',
                'due_date' => $data['due_date'],
            ]);

            $syncData = [];

            foreach ($data['product_id'] as $index => $productId) {
                $syncData[$productId] = [
                    'quantity' => $data['quantity'][$index],
                ];
            }

            $sale->products()->sync($syncData);

            return $sale;
            
        });
    }
}