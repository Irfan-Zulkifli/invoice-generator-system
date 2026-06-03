<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Builder $builder)
    {
        $title = 'Products';
        $breadcrumbs = [
            'Home' => route('dashboard'),
            'Products' => route('products.index'),
        ];
        $button_create = '<a href="'.route('products.create').'" class="btn btn-primary"><i class="fas fa-plus"></i> Create Product</a>';

        $products = Product::with('creator', 'inventoryMovements')->where('creator_id', auth()->id())->get();

        if (request()->ajax()) {
            $products = Product::with('creator')->where('creator_id', auth()->id());

            if (request()->filled('start_date')) {
                $products->whereDate('created_at', '>=', request('start_date'));
            }
            if (request()->filled('end_date')) {
                $products->whereDate('created_at', '<=', request('end_date'));
            }

            return DataTables::of($products)
                ->addColumn('total', function ($product) {
                    $isLowStock = $product->current_stock <= $product->min_stock;

                    if ($isLowStock) {
                        return '<span class="badge bg-danger font-size-12 py-1 px-2">
                                    <i class="bx bx-error-circle align-middle me-1"></i>' 
                                    . $product->current_stock . ' (Low Stock)
                                </span>';
                    }

                    return '<span class="badge bg-success font-size-12 py-1 px-2">' 
                            . $product->current_stock . '
                            </span>';
                })
                ->addColumn('actions', function ($product) {
                    $editUrl = route('products.edit', $product);
                    $deleteUrl = route('products.destroy', $product);

                    $addStock = '
                        <button class="btn btn-sm btn-success waves-effect waves-light" title="Add Stock" data-bs-toggle="modal" data-bs-target="#addStockModal" data-id="'. $product->id .'" data-title='. $product->name .'>
                            <i class="bx bx-plus-circle"></i>
                        </button>
                    ';

                    $decreaseStock = '
                        <button class="btn btn-sm btn-warning waves-effect waves-light" title="Decrease Stock" data-bs-toggle="modal" data-bs-target="#decreaseStockModal" data-id="'. $product->id .'" data-title='. $product->name .'>
                            <i class="bx bx-minus-circle"></i>
                        </button>
                    ';

                    // Edit Button (Blue with icon)
                    $editBtn = '<a href="'.$editUrl.'" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                    <i class="bx bx-edit-alt"></i>
                                </a>';
                                
                    // Delete Button (Red with icon, triggers JS)
                    $deleteBtn = '<button type="button" onclick="deleteProduct('.$product->id.')" class="btn btn-sm btn-danger waves-effect waves-light" title="Delete">
                                    <i class="bx bx-trash"></i>
                                </button>';
                                
                    // Hidden Form for secure deletion
                    $deleteForm = '<form id="delete-form-'.$product->id.'" action="'.$deleteUrl.'" method="POST" style="display: none;">
                                        '.csrf_field().'
                                        '.method_field('DELETE').'
                                </form>';

                    // Wrap them in a d-flex container with a gap
                    return '<div class="d-flex align-items-center gap-2">'.$addStock.$decreaseStock.$editBtn.$deleteBtn.$deleteForm.'</div>';
                })
                ->addIndexColumn()
                ->rawColumns(['actions', 'total'])
                ->make(true);
        }

        $dataTable = $builder->columns([
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => '#', 'orderable' => false, 'searchable' => false],
            ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
            ['data' => 'description', 'name' => 'description', 'title' => 'Description'],
            ['data' => 'price', 'name' => 'price', 'title' => 'Price'],
            ['data' => 'min_stock', 'name' => 'min_stock', 'title' => 'Min Stock'],
            ['data' => 'total', 'name' => 'total', 'title' => 'Total'],
            ['data' => 'actions', 'name' => 'actions', 'title' => 'Actions', 'orderable' => false, 'searchable' => false],
        ])
            ->ajax([
                'data' => 'function(d) {
                    d.start_date = $("#start_date").val();
                    d.end_date = $("#end_date").val();
                }'
            ]);

        return view('pages.products.index', compact('title', 'breadcrumbs', 'dataTable', 'button_create'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Create Product';
        $breadcrumbs = [
            'Home' => route('dashboard'),
            'Products' => route('products.index'),
            'Create' => route('products.create'),
        ];

        $product = new Product();

        return view('pages.products.create', compact('title', 'breadcrumbs', 'product'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'min_stock' => 'nullable|numeric'
        ], [
            'name.required' => 'Product name is required',
            'name.string' => 'Product name must be a string',
            'name.max' => 'Product name cannot exceed 255 characters',
            'description.string' => 'Description must be a string',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
            'min_stock.numeric' => 'Minimum stock must be a number',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'min_stock' => $request->min_stock,
            'creator_id' => auth()->id(),
        ]);

        activity()
            ->performedOn($product)
            ->withProperties(['attributes' => $product->toArray()])
            ->log('created');

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $title = 'Edit Product';
        $breadcrumbs = [
            'Home' => route('dashboard'),
            'Products' => route('products.index'),
            'Edit' => route('products.edit', $product),
        ];

        return view('pages.products.create', compact('title', 'breadcrumbs', 'product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'min_stock' => 'nullable|numeric'
        ], [
            'name.required' => 'Product name is required',
            'name.string' => 'Product name must be a string',
            'name.max' => 'Product name cannot exceed 255 characters',
            'description.string' => 'Description must be a string',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
            'min_stock.numeric' => 'Minimum stock must be a number',
        ]);

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'min_stock' => $request->min_stock,
        ]);

        activity()
            ->performedOn($product)
            ->withProperties([
                'attributes' => $product->getChanges(),
                'old' => collect($product->getOriginal())->only(array_keys($product->getChanges()))->toArray()
            ])
            ->log('updated');

        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $productData = $product->toArray();
        $product->delete();

        activity()
            ->performedOn($product)
            ->withProperties(['attributes' => $productData])
            ->log('deleted');

        return response()->json([
            'status' => true,
            'message' => 'Product Deleted Successfully.',
            'redirect' => route('products.index'),
        ]);
    }

    public function addStock(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ], [
            'product_id.required' => 'Product ID is required',
            'product_id.exists' => 'We could not find a product matching that ID.',
            'quantity.required' => 'Quantity to be added is required',
            'quantity.integer' => 'Quantity must be a number',
            'quantity.min' => 'You must add at least 1 item',
        ]);

        $inventoryMovement = InventoryMovement::create([
            'product_id' => $validated['product_id'],
            'user_id' => auth()->id(),
            'movement_type' => 'add',
            'quantity' => $validated['quantity'],
            'reference_notes' => $validated['notes']
        ]);

        $inventoryMovement->save();

        activity()
            ->performedOn($inventoryMovement)
            ->withProperties(['attributes' => $inventoryMovement->toArray()])
            ->log('added');

        return redirect()->back()->with('success', 'Successfully added the product stock.');
        
    }

    public function decreaseStock(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ], [
            'product_id.required' => 'Product ID is required',
            'product_id.exists' => 'We could not find a product matching that ID.',
            'quantity.required' => 'Quantity to be decrease is required',
            'quantity.integer' => 'Quantity must be a number',
            'quantity.min' => 'You must decrease at least 1 item',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $currentStockNum = $product->current_stock;

        $inventoryMovement = new InventoryMovement;

        $inventoryMovement->fill([
            'product_id' => $validated['product_id'],
            'user_id' => auth()->id(),
            'movement_type' => 'add',
            'quantity' => $validated['quantity'] * -1,
            'reference_notes' => $validated['notes']
        ]);

        if (($currentStockNum - $validated['quantity']) < 0) {
            return redirect()->back()->with('error', 'You cannot decrease stock below zero! Current stock is only ' . $currentStockNum);
        }

        $inventoryMovement->save();

        activity()
            ->performedOn($inventoryMovement)
            ->withProperties(['attributes' => $inventoryMovement->toArray()])
            ->log('decreased');

        return redirect()->back()->with('success', 'Successfully decreased the product stock.');
    }

    public function getProductQuantity($productId, \Illuminate\Http\Request $request) {

        $product = Product::findOrFail($productId);
        $available = $product->current_stock;

        if ($request->has('sale_id') && !empty($request->sale_id)) {
            $saleItem = \App\Models\SaleItem::where('sale_id', $request->sale_id)
                                            ->where('product_id', $productId)
                                            ->first();
            if ($saleItem) {
                $available += $saleItem->quantity;
            }
        }

        return response()->json([
            'status' => 'success',
            'product_quantity' => $available,
            'product_selected_id' => $product->id,
        ]);
    }
}
