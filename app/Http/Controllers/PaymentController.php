<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder;

class PaymentController extends Controller
{
    public function getPaymentsBySale(Sale $sale, Builder $builder) 
    {
        $title = 'Payments';
        $breadcrumbs = [
            'Home' => route('template'),
            'Sales' => route('sales.index'),
            'Payments' => route('payments.index'),
        ];

        $button_create = '<a href="'.route('payments.create').'" class="btn btn-primary"><i class="fas fa-plus"></i> Add Payment Record</a>';

        if (request()->ajax()) {
            $payments = Payment::where('sale_id' ,$sale->id)->get();

            return Datatables::of($payments)
                ->addColumn('actions', function ($payment) {
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
        ])
        ->parameters([
            'dom' => 'Bfrtip',
            'buttons' => ['copy', 'csv', 'excel', 'pdf', 'print']
        ])
        ->minifiedAjax();

        return view('pages.payments.index', compact('title', 'breadcrumbs', 'dataTable', 'button_create'));
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
