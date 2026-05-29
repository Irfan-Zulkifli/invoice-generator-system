<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder;

class InventoryMovementController extends Controller
{
    public function index(Builder $builder)
    {
        $title = 'Product Inventory';

        $breadcrumbs = [
            'Home' => route('dashboard'),
            'Product Inventory' => route('inventories.index'),
        ];

        if (request()->ajax()) {

            $products = Product::with('inventoryMovements')
                                ->where('creator_id', auth()->user()->id);
            
            return DataTables::of($products)
                ->addColumn('action', function ($product) {
                    
                })
                ->addIndexColumn()
                ->make(true);
            
        }

        $dataTable = $builder->columns([
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => '#', 'orderable' => false, 'searchable' => false],
            ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
            ['data' => 'description', 'name' => 'description', 'title' => 'Description'],
            ['data' => 'price', 'name' => 'price', 'title' => 'Price'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action']
        ]);

        return view('pages.inventories.index', compact('dataTable', 'title', 'breadcrumbs'));

    }
}
