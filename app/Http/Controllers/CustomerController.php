<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Builder $builder)
    {
        $title = 'Customers';
        $breadcrumbs = [
            'Home' => route('template'),
            'Products' => route('customers.index'),
        ];
        $button_create = '<a href="'.route('customers.create').'" class="btn btn-primary"><i class="fas fa-plus"></i> Add Customer</a>';

        if (request()->ajax()) {
            $products = Customer::with('seller')->where('seller_id', auth()->id());

            return DataTables::of($products)
                ->addColumn('actions', function ($product) {
                    return '<a href="'.route('customers.edit', $product).'" class="btn btn-success">Edit</a> <a href="'.route('customers.destroy', $product).'" class="btn btn-danger">Delete</a>';
                })
                ->addIndexColumn()
                ->rawColumns(['actions'])
                ->make(true);
        }

        $dataTable = $builder->columns([
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => '#', 'orderable' => false, 'searchable' => false],
            ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
            ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
            ['data' => 'phone', 'name' => 'phone', 'title' => 'Phone'],
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
        $title = 'Create Customer';
        $breadcrumbs = [
            'Home' => route('template'),
            'Customers' => route('customers.index'),
            'Create' => route('customers.create'),
        ];

        $customer = new Customer();

        return view('pages.customers.create', compact('title', 'breadcrumbs', 'customer'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:9',
            'address' => 'nullable|string',
        ], [
            'name.required' => 'Customer name is required',
            'name.string' => 'Customer name must be a string',
            'name.max' => 'Customer name cannot exceed 255 characters',
            'email.email' => 'Email must follow the email format',
            'phone.max' => 'Phone Number cannot exceed 9 number. Exclude the "-" sign',
            'address.string' => 'String must be a string',
        ]);
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
