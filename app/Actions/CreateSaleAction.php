<?php

namespace App\Actions;

use App\Models\Customer;
use App\Models\InventoryMovement;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class CreateSaleAction {

    public function execute(Array $data)
    {
        
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
                $quantity = (int) $data['quantity'][$index];

                if (isset($syncData[$productId])) {
                    $syncData[$productId]['quantity'] += $quantity;
                } else {
                    $syncData[$productId] = [
                        'quantity' => $quantity
                    ];
                }

            }

            // add inventory movement movement_type subtract

            foreach ($syncData as $productId => $details) {
                $soldQuantity = $details['quantity'];
                $inventoryMovement = InventoryMovement::create([
                    'product_id' => $productId,
                    'user_id' => auth()->id(),
                    'movement_type' => 'subtract',
                    'quantity' => $soldQuantity * -1,
                    'reference_notes' => 'Sale created: #' . $sale->id,
                ]);
            } 

            $changes = $sale->products()->sync($syncData);

            $logProperties = [
                'attached_products' => [],
                'updated_products' => [],
                'detached_products' => [],
            ];

            $summaryDescription = [];

            if (!empty($changes['attached'])) {
                foreach ($changes['attached'] as $productId) {
                    $qty = $syncData[$productId]['quantity'];
                    $logProperties['attached_products'][] = ['product_id' => $productId, 'quantity' => $qty];
                }
                $summaryDescription[] = "Added " . count($changes['attached']) . " new product line items";
            }

            if (!empty($changes['updated'])) {
                foreach ($changes['updated'] as $productId) {
                    $qty = $syncData[$productId]['quantity'];
                    $logProperties['updated_products'][] = ['product_id' => $productId, 'quantity' => $qty];
                }
                $summaryDescription[] = "Updated quantities for " . count($changes['updated']) . " products";
            }

            if (!empty($changes['detached'])) {
                foreach ($changes['detached'] as $productId) {
                    $logProperties['detached_products'][] = ['product_id' => $productId];
                }
                $summaryDescription[] = "Removed " . count($changes['detached']) . " items from order";
            }

            $hasChanges = !empty($changes['attached']) || !empty($changes['updated']) || !empty($changes['detached']);

            if ($hasChanges) {
                activity()
                    ->performedOn($sale)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'attributes' => $logProperties,
                        'raw_sync_receipt' => $changes,
                    ])
                    ->log("Added sale order details: " . implode(', ', $summaryDescription));
            }

            return $sale;

        });
    }
}