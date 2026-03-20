<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Builder $builder)
    {
        $title = 'Sales';
        $breadcrumbs = [
            'Home' => route('template'),
            'Sales' => route('sales.index'),
        ];
        $button_create = '<a href="'.route('sales.create').'" class="btn btn-primary"><i class="fas fa-plus"></i> Add Sale</a>';

        if (request()->ajax()) {
            $sales = Sale::with(['buyer', 'seller'])->where('user_id', auth()->id());

            return DataTables::of($sales)
                ->addColumn('actions', function ($sale) {
                    $editUrl = route('sales.edit', $sale);
                    $deleteUrl = route('sales.destroy', $sale);

                    // Edit Button (Blue with icon)
                    $editBtn = '<a href="'.$editUrl.'" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                    <i class="bx bx-edit-alt"></i>
                                </a>';
                                
                    // Delete Button (Red with icon, triggers JS)
                    $deleteBtn = '<button type="button" onclick="deleteCustomer('.$sale->id.')" class="btn btn-sm btn-danger waves-effect waves-light" title="Delete">
                                    <i class="bx bx-trash"></i>
                                </button>';
                                
                    // Hidden Form for secure deletion
                    $deleteForm = '<form id="delete-form-'.$sale->id.'" action="'.$deleteUrl.'" method="POST" style="display: none;">
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
            ['data' => 'buyer.name', 'name' => 'buyer.name', 'title' => 'Customer'],
            ['data' => 'total_amount', 'name' => 'total_amount', 'title' => 'Amount'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
            ['data' => 'actions', 'name' => 'actions', 'title' => 'Actions', 'orderable' => false, 'searchable' => false],
        ])
            ->minifiedAjax();

        return view('pages.sales.index', compact('title', 'breadcrumbs', 'dataTable', 'button_create'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Create Customer';
        $breadcrumbs = [
            'Home' => route('template'),
            'Sales' => route('sales.index'),
            'Create' => route('sales.create'),
        ];

        $sale = new Sale();
        $existingCustomers = Customer::with('seller')->where('seller_id', auth()->id())->get();
        $products = Product::where('creator_id', auth()->id())->get();

        return view('pages.sales.create', compact('title', 'breadcrumbs', 'sale', 'existingCustomers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        if($request->formRadios == 'yes') {
            $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'product_id' => 'required|array',
                'product_id.*' => 'exists:products,id',
                'quantity' => 'required|array',
                'quantity.*' => 'numeric|regex:/[0-9]/i'
            ], [
                'customer_id.required' => 'You are required to pick a customer.',
                'customer_id.exists' => 'Customer does not exists.',
                'product_id.required' => 'You are required to pick at least one product.',
                'product_id.array' => 'Your product selection is invalid.',
                'product_id.*.exists' => 'Your selected product already exists',
                'quantity.required' => 'You must provide a quantity for the products.',
                'quantity.array' => 'The quantity format is invalid.',
                'quantity.*.numeric' => 'Every quantity provided must be a number.',
                'quantity.*.regex' => 'The quantity must contain only valid digits.'
            ]);
        } else {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone' => 'nullable|string|max:10',
                'address' => 'nullable|string',
                'product_id' => 'required|array',
                'product_id.*' => 'exists:products,id',
                'quantity' => 'required|array',
                'quantity.*' => 'numeric|regex:/[0-9]/i'
            ], [
                'name.required' => 'Customer name is required',
                'name.string' => 'Customer name must be a string',
                'name.max' => 'Customer name cannot exceed 255 characters',
                'email.email' => 'Email must follow the email format',
                'phone.max' => 'Phone Number cannot exceed 9 number. Exclude the "-" sign',
                'address.string' => 'String must be a string',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        //
    }
}
