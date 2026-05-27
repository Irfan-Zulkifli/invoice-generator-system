@extends('layouts.app')
@section('content')

    {{-- 1. Calculate the financial variables upfront --}}
    @php
        $totalPrice = $sale->total_price;
        $totalPaid = $sale->payments->sum('amount');
        $balanceDue = $totalPrice - $totalPaid;
        
        // Calculate percentage for the progress bar (prevent division by zero)
        $progressPercentage = $totalPrice > 0 ? ($totalPaid / $totalPrice) * 100 : 0;
        // Cap it at 100% just in case of overpayment
        $progressPercentage = min(100, $progressPercentage); 
    @endphp

    <div class="row">
        <div class="col-12">
            
            {{-- PAGE HEADER WITH ACTION BUTTONS --}}
            {{-- <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="mb-0 font-size-18">Sale Payments</h4>
                <div>
                    <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-secondary waves-effect waves-light me-1">
                        <i class="bx bx-arrow-back font-size-16 align-middle me-2"></i> Back to Sale
                    </a>
                    @if($balanceDue > 0)
                        <a href="{{ route('payments.create', ['sale_id' => $sale->id]) }}" class="btn btn-primary waves-effect waves-light">
                            <i class="bx bx-plus font-size-16 align-middle me-2"></i> Record Payment
                        </a>
                    @endif
                </div>
            </div> --}}

            {{-- FINANCIAL SUMMARY CARD --}}
            <div class="card mb-4 border-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        
                        {{-- Col 1: Customer & Status --}}
                        <div class="col-md-3">
                            <h5 class="font-size-15 mb-2">Sale #{{ $sale->id }}</h5>
                            <p class="text-muted mb-1">Customer: <span class="fw-medium text-dark">{{ $sale->buyer->name ?? 'N/A' }}</span></p>
                            <p class="text-muted mb-0">Due Date: 
                                <span class="fw-medium {{ $sale->due_date && $sale->due_date->isPast() && $balanceDue > 0 ? 'text-danger' : 'text-dark' }}">
                                    {{ $sale->due_date ? $sale->due_date->format('d M Y') : 'N/A' }}
                                </span>
                            </p>
                        </div>

                        {{-- Col 2: Total Billed --}}
                        <div class="col-md-3 text-md-center mt-3 mt-md-0">
                            <p class="text-muted mb-2">Total Amount</p>
                            <h4 class="mb-0">RM {{ number_format($totalPrice, 2) }}</h4>
                        </div>

                        {{-- Col 3: Total Paid --}}
                        <div class="col-md-3 text-md-center mt-3 mt-md-0">
                            <p class="text-muted mb-2">Total Paid</p>
                            <h4 class="mb-0 text-success">RM {{ number_format($totalPaid, 2) }}</h4>
                        </div>

                        {{-- Col 4: Balance Due & Status Badge --}}
                        <div class="col-md-3 text-md-end mt-3 mt-md-0">
                            <p class="text-muted mb-2">Balance Due</p>
                            <h4 class="mb-2 {{ $balanceDue > 0 ? 'text-danger' : 'text-muted' }}">
                                RM {{ number_format(max(0, $balanceDue), 2) }}
                            </h4>
                            <div>
                                @if ($sale->status->label() == 'paid')
                                    <span class="badge badge-pill badge-soft-success font-size-12 px-3 py-1">{{ ucwords(str_replace('_', ' ', $sale->status->label())) }}</span>
                                @elseif ($sale->status->label() == 'partially_paid')
                                    <span class="badge badge-pill badge-soft-warning font-size-12 px-3 py-1">{{ ucwords(str_replace('_', ' ', $sale->status->label())) }}</span>
                                @else
                                    <span class="badge badge-pill badge-soft-danger font-size-12 px-3 py-1">{{ ucwords(str_replace('_', ' ', $sale->status->label())) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="font-size-13 text-muted">Payment Progress</span>
                            <span class="font-size-13 text-muted">{{ number_format($progressPercentage, 0) }}%</span>
                        </div>
                        <div class="progress progress-sm" style="height: 6px;">
                            <div class="progress-bar {{ $progressPercentage == 100 ? 'bg-success' : 'bg-primary' }}" 
                                 role="progressbar" 
                                 style="width: {{ $progressPercentage }}%" 
                                 aria-valuenow="{{ $progressPercentage }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>

            @include('components.date-filter')
            
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Payment History</h4>
                    
                    {{-- Table container --}}
                    <div class="table-responsive">
                        {{ $dataTable->table(['class' => 'table table-bordered dt-responsive nowrap w-100 yajra-datatable']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {!! $dataTable->scripts() !!}
    <script>
        $(document).ready(function() {
            $('#btn-filter').on('click', function() {
                let tableId = Object.keys(window.LaravelDataTables)[0];
                window.LaravelDataTables[tableId].draw();
            });
            $('#btn-reset').on('click', function() {
                $('#start_date').val('');
                $('#end_date').val('');
                let tableId = Object.keys(window.LaravelDataTables)[0];
                window.LaravelDataTables[tableId].draw();
            });
        });

        function deleteSale(id) {
            Swal.fire({
                title: "Are you sure want to delete this?",
                text: "You won't be able to revert this.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonClass: 'btn btn-primary w-xs me-2 mt-2',
                cancelButtonClass: 'btn btn-danger w-xs mt-2',
                confirmButtonText: "Yes, delete it!",
                showCloseButton: true
            }).then(function(result) {
                if (result.isConfirmed) {
                    let formElement = document.getElementById(`delete-form-${id}`);
                    let formData = new FormData(formElement);
                    let actionUrl = formElement.getAttribute('action');

                    fetch(actionUrl, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonClass: 'btn btn-primary w-xs mt-2',
                                }).then(() => {
                                    window.LaravelDataTables[Object.keys(window.LaravelDataTables)[0]].draw();
                                    window.location.href = data.redirect;
                                })
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        });
                }
            })
        }
    </script>
@endsection