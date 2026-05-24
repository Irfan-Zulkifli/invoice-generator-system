<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Sale;
use App\Rules\PaymentCheck;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder;

class PaymentController extends Controller
{
    public function getPaymentsBySale(Sale $sale, Builder $builder) 
    {
        $title = 'Payments: Sale #' . $sale->id;
        $breadcrumbs = [
            'Home' => route('dashboard'),
            'Sales' => route('sales.index'),
            'Payments: Sale #' . $sale->id => route('sales.payments', $sale),
        ];

        $button_create = '<a href="'.route('sales.payments.create', $sale).'" class="btn btn-primary"><i class="fas fa-plus"></i> Add Payment Record</a>';

        if (request()->ajax()) {
            $payments = Payment::where('sale_id' ,$sale->id);

            if (request()->filled('start_date')) {
                $payments->whereDate('payment_date', '>=', request('start_date'));
            }
            if (request()->filled('end_date')) {
                $payments->whereDate('payment_date', '<=', request('end_date'));
            }

            return DataTables::of($payments)
                ->editColumn('payment_method', function ($payment) {
                    return str_replace('_', ' ', $payment->payment_method);
                })
                ->addColumn('actions', function ($payment) use ($sale) {
                    $editUrl = route('payments.edit', $payment);
                    $deleteUrl = route('payments.destroy', $payment);
                    $viewUrl = route('payments.show', $payment);

                    $viewBtn = '<a href="'.$viewUrl.'" class="btn btn-sm btn-secondary waves-effect waves-light" title="Edit">
                                    <i class="bx bx-show-alt"></i>
                                </a>';

                    // Edit Button (Blue with icon)
                    $editBtn = '<a href="'.$editUrl.'" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                                    <i class="bx bx-edit-alt"></i>
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

                    // Wrap them in a d-flex container with a gap
                    return '<div class="d-flex align-items-center gap-2">'.$viewBtn.($sale->status->label() == 'unpaid' ? $editBtn : '').$deleteBtn.$deleteForm.'</div>';
                })
                ->addIndexColumn()
                ->rawColumns(['actions'])
                ->make(true);
        }

        $dataTable = $builder->columns([
            ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => '#', 'orderable' => false, 'searchable' => false],
            ['data' => 'amount', 'name' => 'amount', 'title' => 'Amount'],
            ['data' => 'payment_date', 'name' => 'payment_date', 'title' => 'Payment Date'],
            ['data' => 'payment_method', 'name' => 'payment_method', 'title' => 'Payment Method'],
            ['data' => 'actions', 'name' => 'actions', 'title' => 'Action']
        ])
        ->parameters([
            'dom' => 'Bfrtip',
            'buttons' => ['copy', 'csv', 'excel', 'pdf', 'print']
        ])
        ->ajax([
            'data' => 'function(d) {
                d.start_date = $("#start_date").val();
                d.end_date = $("#end_date").val();
            }'
        ]);

        return view('pages.payments.index', compact('title', 'breadcrumbs', 'dataTable', 'button_create'));
    }

    public function createPaymentRecord(Sale $sale)
    {
        $title = 'Create Payment Record: Sale #' . $sale->id;
        $breadcrumbs = [
            'Home' => route('dashboard'),
            'Sales' => route('sales.index'),
            'Payments: Sale #' . $sale->id => route('sales.payments', $sale),
            'Create Payment: Sale #' . $sale->id => route('sales.payments.create', $sale)
        ];

        $payment = new Payment();

        return view('pages.payments.create', compact('payment', 'title', 'breadcrumbs', 'sale'));
    }

    public function addPaymentRecord(Sale $sale, Request $request)
    {
        // $sale = Sale::findOrFail($request->sale_id);
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'payment_date' => 'required|date_format:d/m/Y',
            'amount' => ['required', 'numeric', 'min:0', new PaymentCheck($sale)],
            'payment_method' => 'required|in:tunai,pindahan_bank,kad_kredit,cek',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['payment_date'] = Carbon::createFromFormat('d/m/Y', $validated['payment_date'])->format('Y-m-d');

        $validated['recorded_by'] = auth()->user()->id;

        Payment::create($validated);

        $sale->status = $sale->status_after_payment;
        $sale->save();

        return redirect()->route('sales.payments', $sale)->with('success', 'Payments created successfully.');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $isView = true;
        $sale = $payment->sale;

        $title = 'View Payment Record: Sale #' . $sale->id;
        $breadcrumbs = [
            'Home' => route('dashboard'),
            'Sales' => route('sales.index'),
            'Payments: Sale #' . $sale->id => route('sales.payments', $sale),
            'View Payment: Sale #' . $sale->id => route('payments.show', $sale)
        ];

        return view('pages.payments.create', compact('isView', 'payment', 'sale', 'title', 'breadcrumbs'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        $isEdit = true;
        $sale = $payment->sale;

        $title = 'Edit Payment Record: Sale #' . $sale->id;
        $breadcrumbs = [
            'Home' => route('dashboard'),
            'Sales' => route('sales.index'),
            'Payments: Sale #' . $sale->id => route('sales.payments', $sale),
            'Edit Payment: Sale #' . $sale->id => route('payments.edit', $sale)
        ];

        return view('pages.payments.create', compact('isEdit', 'payment', 'sale', 'title', 'breadcrumbs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'payment_date' => 'required|date_format:d/m/Y',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:tunai,pindahan_bank,kad_kredit,cek',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $sale = $payment->sale;

        $validated['payment_date'] = Carbon::createFromFormat('d/m/Y', $validated['payment_date'])->format('Y-m-d');

        $payment->update($validated);

        return redirect()->route('sales.payments', $sale)->with('success', 'Payments created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        $sale = $payment->sale;
        $payment->delete();

        return response()->json([
            'status' => true,
            'message' => 'Payment Deleted Successfully',
            'redirect' => route('sales.payments', $sale),
        ]);
    }
}
