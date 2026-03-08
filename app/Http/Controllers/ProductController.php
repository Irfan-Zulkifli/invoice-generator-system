<?php

namespace App\Http\Controllers;

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
            'Home' => route('template'),
            'Products' => route('products.index'),
        ];
        $button_create = '<a href="'.route('products.create').'" class="btn btn-primary"><i class="fas fa-plus"></i> Create Product</a>';

        $products = Product::with('creator')->where('creator_id', auth()->id())->get();

        if (request()->ajax()) {
            $products = Product::with('creator')->where('creator_id', auth()->id());

            return DataTables::of($products)
                ->addColumn('actions', function ($product) {
                    $editUrl = route('products.edit', $product);
                    $deleteUrl = route('products.destroy', $product);

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
                    return '<div class="d-flex align-items-center gap-2">'.$editBtn.$deleteBtn.$deleteForm.'</div>';
                })
                ->addIndexColumn()
                ->rawColumns(['actions'])
                ->make(true);
        }

        $dataTable = $builder->columns([
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => '#', 'orderable' => false, 'searchable' => false],
            ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
            ['data' => 'description', 'name' => 'description', 'title' => 'Description'],
            ['data' => 'price', 'name' => 'price', 'title' => 'Price'],
            ['data' => 'actions', 'name' => 'actions', 'title' => 'Actions', 'orderable' => false, 'searchable' => false],
        ])
            ->minifiedAjax();

        return view('pages.products.index', compact('title', 'breadcrumbs', 'dataTable', 'button_create'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Create Product';
        $breadcrumbs = [
            'Home' => route('template'),
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
        ], [
            'name.required' => 'Product name is required',
            'name.string' => 'Product name must be a string',
            'name.max' => 'Product name cannot exceed 255 characters',
            'description.string' => 'Description must be a string',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
        ]);

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'creator_id' => auth()->id(),
        ]);

        return redirect()->route('products.index');
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
            'Home' => route('template'),
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
        ], [
            'name.required' => 'Product name is required',
            'name.string' => 'Product name must be a string',
            'name.max' => 'Product name cannot exceed 255 characters',
            'description.string' => 'Description must be a string',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
        ]);

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        return redirect()->route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product Deleted Successfully.',
            'redirect' => route('products.index'),
        ]);
    }
}
