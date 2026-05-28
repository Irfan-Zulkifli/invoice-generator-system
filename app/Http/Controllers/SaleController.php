<?php

namespace App\Http\Controllers;

use App\Actions\CreateSaleAction;
use App\Actions\UpdateSaleAction;
use App\Actions\UpdateSaleRequest;
use App\Http\Requests\CreateSaleRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
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
            'Home' => route('dashboard'),
            'Sales' => route('sales.index'),
        ];
        $slot = '
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label mb-1">Status</label>
                <select class="form-control select2" name="status">
                    <option value="">All</option>
                    <option value="unpaid">Unpaid</option>
                    <option value="paid">Paid</option>
                    <option value="partially_paid">Partially Paid</option>
                </select>
            </div>
        ';
        $button_create = '<a href="'.route('sales.create').'" class="btn btn-primary"><i class="fas fa-plus"></i> Add Sale</a>';

        if (request()->ajax()) {
            $sales = Sale::with(['buyer', 'seller'])->where('user_id', auth()->id());

            if (request()->filled('start_date')) {
                $sales->whereDate('created_at', '>=', request('start_date'));
            }
            if (request()->filled('end_date')) {
                $sales->whereDate('created_at', '<=', request('end_date'));
            }

            if (request()->filled('status')) {
                $sales->where('status', request('status'));
            }

            return DataTables::of($sales)
                ->editColumn('status', function ($sale) {

                    return '<span class="badge badge-pill badge-soft-'.$sale->status->color().' font-size-12 px-3 py-1">'. ucwords(str_replace('_', ' ', $sale->status->label())) . '</span>';

                })
                ->editColumn('created_at', function ($sale) {
                    return $sale->created_at->format('d M Y');
                })
                ->editColumn('due_date', function ($sale) {
                    return $sale->due_date->format('d M Y') . '<br>'. $sale->due_label;
                })
                ->addColumn('actions', function ($sale) {
                    $editUrl = route('sales.edit', $sale);
                    $deleteUrl = route('sales.destroy', $sale);
                    $viewUrl = route('sales.show', $sale);
                    $paymentUrl = route('sales.payments', $sale);
                    $receiptUrl = route('sales.receipt', $sale);

                    $viewBtn = '<a href="'.$viewUrl.'" class="btn btn-sm btn-secondary waves-effect waves-light" title="Edit">
                                    <i class="bx bx-show-alt"></i>
                                </a>';

                    // Edit Button (Blue with icon)
                    $editBtn = '<a href="'.$editUrl.'" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                    <i class="bx bx-edit-alt"></i>
                                </a>';

                    $updatePaymentBtn = '<a href="'.$paymentUrl.'" class="btn btn-sm btn-info waves-effect waves-light" title="Update Payment">
                                    <i class="bx bx-money"></i>
                                </a>';
                                
                    // Delete Button (Red with icon, triggers JS)
                    $deleteBtn = '<button type="button" onclick="deleteSale('.$sale->id.')" class="btn btn-sm btn-danger waves-effect waves-light" title="Delete">
                                    <i class="bx bx-trash"></i>
                                </button>';
                                
                    // Hidden Form for secure deletion
                    $deleteForm = '<form id="delete-form-'.$sale->id.'" action="'.$deleteUrl.'" method="POST" style="display: none;">
                                        '.csrf_field().'
                                        '.method_field('DELETE').'
                                </form>';

                    $receipt = '<a href="'.$receiptUrl.'" class="btn btn-sm btn-warning waves-effect waves-light" title="Print Receipt">
                                    <i class="bx bx-receipt"></i>
                                </a>';

                    // Wrap them in a d-flex container with a gap
                    return '<div class="d-flex align-items-center gap-2">'.$viewBtn.($sale->status->label() == 'unpaid' ? $editBtn : '').($sale->status->label() == 'paid' ? $receipt : '').$updatePaymentBtn.$deleteBtn.$deleteForm.'</div>';
                })
                ->addIndexColumn()
                ->rawColumns(['actions', 'status', 'due_date'])
                ->make(true);
        }

        $dataTable = $builder->columns([
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => '#', 'orderable' => false, 'searchable' => false],
            ['data' => 'buyer.name', 'name' => 'buyer.name', 'title' => 'Customer', 'orderable' => false, 'searchable' => false],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'],
            ['data' => 'due_date', 'name' => 'due_date', 'title' => 'Payment Due Date'],
            ['data' => 'actions', 'name' => 'actions', 'title' => 'Actions', 'orderable' => false, 'searchable' => false],
        ])
            ->ajax([
                'data' => 'function(d) {
                    d.start_date = $("#start_date").val();
                    d.end_date = $("#end_date").val();
                    d.status = $("[name=\'status\']").val();
                }'
            ]);

        return view('pages.sales.index', compact('title', 'breadcrumbs', 'dataTable', 'button_create', 'slot'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Create Sale';
        $breadcrumbs = [
            'Home' => route('dashboard'),
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
    public function store(CreateSaleRequest $saleRequest, CreateSaleAction $action)
    {

        $sale = $action->execute($saleRequest->validated());
        
        activity()
            ->performedOn($sale)
            ->withProperties(['attributes' => $sale->toArray()])
            ->log('created');

        return redirect()->route('sales.index')->with('success', 'Sale created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        $sale->load(['buyer', 'products', 'payments']);
    
        $title = 'View Sale #' . $sale->id;
        $breadcrumbs = [
            'Home' => route('dashboard'),
            'Sales' => route('sales.index'),
            'View' => route('sales.show', $sale),
        ];

        $paymentsTable = $sale->payments()->paginate(5);

        $saleActivities = Activity::forSubject($sale)->get();

        $paymentActivities = \Spatie\Activitylog\Models\Activity::where('subject_type', \App\Models\Payment::class)
            ->whereIn('subject_id', $sale->payments->pluck('id'))
            ->get();

        $auditLogs = $saleActivities->concat($paymentActivities)->sortByDesc('created_at');

        return view('pages.sales.show', compact('title', 'breadcrumbs', 'sale', 'paymentsTable', 'auditLogs'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        $title = 'Edit Sale';
        $breadcrumbs = [
            'Home' => route('dashboard'),
            'Sales' => route('sales.index'),
            'Edit' => route('sales.edit', $sale),
        ];

        $existingCustomers = Customer::with('seller')->where('seller_id', auth()->id())->get();
        $products = Product::where('creator_id', auth()->id())->get();

        return view('pages.sales.create', compact('title', 'breadcrumbs', 'sale', 'existingCustomers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSaleAction $action, Sale $sale, CreateSaleRequest $saleRequest)
    {
        if (!$sale->exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during processing.'
            ]);
        }

        $oldAttributes = $sale->getOriginal();

        $action->execute($sale, $saleRequest->validated());

        activity()
            ->performedOn($sale)
            ->withProperties([
                'attributes' => $sale->getChanges(),
                'old' => collect($oldAttributes)->only(array_keys($sale->getChanges()))->toArray()
            ])
            ->log('updated');

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully!');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        $saleData = $sale->toArray();
        $sale->delete();

        activity()
            ->performedOn($sale)
            ->withProperties(['attributes' => $saleData])
            ->log('deleted');

        return response()->json([
            'status' => true,
            'message' => 'Sale Deleted Successfully.',
            'redirect' => route('sales.index'),
        ]);
    }

    public function receipt(Sale $sale)
    {
        $title = 'Offical Receipt: Sale #' . $sale->id;
        $breadcrumbs = [
            'Home' => route('dashboard'),
            'Sales' => route('sales.index'),
            'Receipt' => false,
        ];
        return view('pages.sales.receipt', compact('sale', 'title', 'breadcrumbs'));
    }
}
