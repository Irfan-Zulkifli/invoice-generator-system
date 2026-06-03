<?php

namespace App\Actions;

use App\Models\Customer;
use App\Models\InventoryMovement;
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

            $oldQuantities = $sale->products()->pluck('sale_items.quantity', 'products.id')->toArray();

            // 1. Handle Added and Updated products
            foreach($syncData as $productId => $details) {
                $newQuantity = $details['quantity'];

                $isExist = array_key_exists($productId, $oldQuantities);

                if ($isExist) {
                    $oldQty = $oldQuantities[$productId];
                    $difference = abs($newQuantity - $oldQty);

                    if ($difference > 0) {
                        if ($newQuantity > $oldQty) {
                            // Quantity increased -> subtract difference from inventory
                            InventoryMovement::create([
                                'product_id' => $productId,
                                'user_id' => auth()->id(),
                                'movement_type' => 'subtract',
                                'quantity' => $difference * -1,
                                'reference_notes' => 'Sale Updated: #' . $sale->id,
                            ]);
                        } else {
                            // Quantity decreased -> add difference back to inventory
                            InventoryMovement::create([
                                'product_id' => $productId,
                                'user_id' => auth()->id(),
                                'movement_type' => 'add',
                                'quantity' => $difference,
                                'reference_notes' => 'Sale Updated: #' . $sale->id,
                            ]);
                        }
                    }
                } else {
                    // Entirely new product added -> subtract full quantity
                    InventoryMovement::create([
                        'product_id' => $productId,
                        'user_id' => auth()->id(),
                        'movement_type' => 'subtract',
                        'quantity' => $newQuantity * -1,
                        'reference_notes' => 'Sale Updated: #' . $sale->id,
                    ]);
                }
            }

            // 2. Handle Removed products (Detached)
            foreach($oldQuantities as $productId => $oldQty) {
                if (!array_key_exists($productId, $syncData)) {
                    // Product completely removed -> add full old quantity back
                    InventoryMovement::create([
                        'product_id' => $productId,
                        'user_id' => auth()->id(),
                        'movement_type' => 'add',
                        'quantity' => $oldQty,
                        'reference_notes' => 'Sale Updated: #' . $sale->id,
                    ]);
                }
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
                    ->log("Updated sale order details: " . implode(', ', $summaryDescription));
            }
            
            return $sale;

        });
    }
}