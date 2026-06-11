@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body" id="receipt-print-area">
                    {{-- Receipt Header --}}
                    <div class="invoice-title">
                        <div class="d-flex align-items-start">
                            <div class="flex-grow-1">
                                
                                {{-- START: CUSTOM LOGO & ICON --}}
                                @if(isset($setting) && (!empty($setting->custom_logo) || !empty($setting->custom_icon)))
                                    <div class="mb-4 d-flex align-items-center">
                                        @if(!empty($setting->custom_icon))
                                            <img src="{{ asset('storage/' . $setting->custom_icon) }}" 
                                                 alt="Company Icon" 
                                                 class="me-2" 
                                                 style="height: 50px; object-fit: contain;">
                                        @endif
                                        
                                        @if(!empty($setting->custom_logo))
                                            <img src="{{ asset('storage/' . $setting->custom_logo) }}" 
                                                 alt="Company Logo" 
                                                 style="height: 50px; object-fit: contain;">
                                        @endif
                                    </div>
                                @endif
                                {{-- END: CUSTOM LOGO & ICON --}}

                                <h3 class="card-title mb-1 fw-bold text-uppercase">Official Receipt</h3>
                                <p class="text-muted font-size-14">Reference: REC-{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</p>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                {{-- Removed the hardcoded logo-dark.png from here to keep it perfectly clean --}}
                                <span class="badge bg-success font-size-14 mt-2">
                                    FULLY PAID
                                </span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    
                    {{-- Customer & Receipt Details --}}
                    <div class="row">
                        <div class="col-sm-6 mt-3">
                            <address>
                                <strong>Received From:</strong><br>
                                <span class="font-size-15 fw-bold">{{ $sale->buyer->name }}</span><br>
                                @if($sale->buyer->email)
                                    {{ $sale->buyer->email }}<br>
                                @endif
                                @if($sale->buyer->phone)
                                    {{ $sale->buyer->phone }}<br>
                                @endif
                            </address>
                        </div>
                        <div class="col-sm-6 mt-3 text-sm-end">
                            <address>
                                <strong>Original Order Date:</strong><br>
                                {{ $sale->created_at->format('d M, Y') }}<br><br>
                                <strong>Receipt Date:</strong><br>
                                {{-- Grabs the date of the final payment made --}}
                                @if($sale->payments->isNotEmpty())
                                    {{ \Carbon\Carbon::parse($sale->payments->sortByDesc('payment_date')->first()->payment_date)->format('d M, Y') }}
                                @else
                                    {{ now()->format('d M, Y') }}
                                @endif
                            </address>
                        </div>
                    </div>

                    {{-- 1. Order Summary (What they bought) --}}
                    <div class="py-2 mt-3">
                        <h5 class="font-size-15 fw-bold text-uppercase border-bottom pb-2">Items Purchased</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 70px;">No.</th>
                                    <th>Item</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end" style="width: 120px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->products as $product)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $product->name }}</td>
                                        <td class="text-center">RM {{ number_format($product->price, 2) }}</td>
                                        <td class="text-center">{{ $product->pivot->quantity }}</td>
                                        <td class="text-end">RM {{ number_format($product->price * $product->pivot->quantity, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- 2. Payment History (How they paid) --}}
                    <div class="py-2 mt-5">
                        <h5 class="font-size-15 fw-bold text-uppercase border-bottom pb-2">Payment Record</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-nowrap align-middle">
                            <thead class="text-muted">
                                <tr>
                                    <th>Date</th>
                                    <th>Method</th>
                                    <th>Reference</th>
                                    <th class="text-end">Amount Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    $totalPaid = 0; 
                                @endphp
                                
                                @foreach($sale->payments as $payment)
                                    @php 
                                        $totalPaid += $payment->amount; 
                                    @endphp
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M, Y') }}</td>
                                        <td class="text-capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                                        <td>{{ $payment->reference_number ?? '-' }}</td>
                                        <td class="text-end">RM {{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- 3. Final Financial Summary --}}
                    @php
                        $grandTotal = $sale->total_price;
                        $balance = $grandTotal - $totalPaid;
                    @endphp
                    <div class="row mt-4">
                        <div class="col-sm-7">
                            <p class="text-muted mb-0"><strong>Note:</strong> Thank you for your business. This is a computer-generated receipt and requires no signature.</p>
                        </div>
                        <div class="col-sm-5">
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-end pb-0">Sub/Grand Total :</td>
                                            <td class="text-end pb-0">RM {{ number_format($grandTotal, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-end pb-0 text-success fw-bold">Total Paid :</td>
                                            <td class="text-end pb-0 text-success fw-bold">- RM {{ number_format($totalPaid, 2) }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="text-end font-size-16 fw-bold">Balance Due :</td>
                                            <td class="text-end font-size-16 fw-bold">RM {{ number_format(max(0, $balance), 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Actions (Hidden when printing) --}}
                    <div class="d-print-none mt-5">
                        <hr>
                        <div class="float-end">
                            <a href="javascript:window.print()" class="btn btn-success waves-effect waves-light me-1">
                                <i class="bx bx-printer me-1"></i> Print Receipt
                            </a>
                        </div>
                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-secondary w-md waves-effect waves-light">
                            <i class="bx bx-arrow-back me-1"></i> Back to Sale
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tiny CSS block strictly for ensuring the printed page looks clean --}}
    <style>
        @media print {
            body { background-color: #fff !important; }
            .card { box-shadow: none !important; border: none !important; }
            .table-light { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; }
            .bg-success { background-color: #34c38f !important; color: #fff !important; -webkit-print-color-adjust: exact; }
        }
    </style>
@endsection