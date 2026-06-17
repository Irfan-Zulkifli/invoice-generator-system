<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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
            'Home' => route('dashboard'),
            'Customers' => route('customers.index'),
        ];
        $button_create = '<a href="'.route('customers.create').'" class="btn btn-primary"><i class="fas fa-plus"></i> Add Customer</a>';

        if (request()->ajax()) {
            $customers = Customer::with('seller')->where('seller_id', auth()->id());

            if (request()->filled('start_date')) {
                $customers->whereDate('created_at', '>=', request('start_date'));
            }
            if (request()->filled('end_date')) {
                $customers->whereDate('created_at', '<=', request('end_date'));
            }

            return DataTables::of($customers)
                ->addColumn('actions', function ($customer) {
                    $editUrl = route('customers.edit', $customer);
                    $deleteUrl = route('customers.destroy', $customer);

                    // Edit Button (Blue with icon)
                    $editBtn = '<a href="'.$editUrl.'" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                    <i class="bx bx-edit-alt"></i>
                                </a>';
                                
                    // Delete Button (Red with icon, triggers JS)
                    $deleteBtn = '<button type="button" onclick="deleteCustomer('.$customer->id.')" class="btn btn-sm btn-danger waves-effect waves-light" title="Delete">
                                    <i class="bx bx-trash"></i>
                                </button>';
                                
                    // Hidden Form for secure deletion
                    $deleteForm = '<form id="delete-form-'.$customer->id.'" action="'.$deleteUrl.'" method="POST" style="display: none;">
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
            ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
            ['data' => 'phone', 'name' => 'phone', 'title' => 'Phone'],
            ['data' => 'actions', 'name' => 'actions', 'title' => 'Actions', 'orderable' => false, 'searchable' => false],
        ])
            ->ajax([
                'data' => 'function(d) {
                    d.start_date = $("#start_date").val();
                    d.end_date = $("#end_date").val();
                }'
            ]);

        return view('pages.customers.index', compact('title', 'breadcrumbs', 'dataTable', 'button_create'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Create Customer';
        $breadcrumbs = [
            'Home' => route('dashboard'),
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
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['nullable', 
                        'email', 
                        Rule::unique('customers', 'email')
                            ->where(function ($query) {
                                return $query->where('seller_id', auth()->id());
                            })
                        ],
            'phone' => 'nullable|string|max:10',
            'address' => 'nullable|string',
        ], [
            'name.required' => 'Customer name is required',
            'name.string' => 'Customer name must be a string',
            'name.max' => 'Customer name cannot exceed 255 characters',
            'email.email' => 'Email must follow the email format',
            'email.exists' => 'Email already in used by other customer.',
            'phone.max' => 'Phone Number cannot exceed 9 number. Exclude the "-" sign',
            'address.string' => 'String must be a string',
        ]);

        $validate['seller_id'] = auth()->id();

        $customer = Customer::create($validate);

        activity()
            ->performedOn($customer)
            ->withProperties(['attributes' => $customer->toArray()])
            ->log('created');

        return redirect()->route('customers.index')->with('success', 'Customer created successfully!');
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
    public function edit(Customer $customer)
    {
        $title = 'Edit Customer';
        $breadcrumbs = [
            'Home' => route('dashboard'),
            'Customers' => route('customers.index'),
            'Edit' => route('customers.edit', $customer),
        ];

        return view('pages.customers.create', compact('title', 'breadcrumbs', 'customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                Rule::unique('customers', 'email')
                     ->where(function ($query) use ($customer) {
                        return $query->where('seller_id', auth()->id());
                     })->ignore($customer->id)
            ],
            'phone' => 'nullable|string|max:10',
            'address' => 'nullable|string',
        ], [
            'name.required' => 'Customer name is required',
            'name.string' => 'Customer name must be a string',
            'name.max' => 'Customer name cannot exceed 255 characters',
            'email.email' => 'Email must follow the email format',
            'phone.max' => 'Phone Number cannot exceed 9 number. Exclude the "-" sign',
            'address.string' => 'String must be a string',
        ]);

        $customer->update($validate);

        activity()
            ->performedOn($customer)
            ->withProperties([
                'attributes' => $customer->getChanges(),
                'old' => collect($customer->getOriginal())->only(array_keys($customer->getChanges()))->toArray()
            ])
            ->log('updated');

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customerData = $customer->toArray();
        $customer->delete();

        activity()
            ->performedOn($customer)
            ->withProperties(['attributes' => $customerData])
            ->log('deleted');

        return response()->json([
            'status' => true,
            'message' => 'Customer Deleted Successfully.',
            'redirect' => route('customers.index'),
        ]);
    }
}
