<?php

namespace App\Actions;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class CreateSaleAction {

    public function execute(Array $data)
    {
        return DB::transaction(function() {

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

            Sale::create();


        });
    }
}