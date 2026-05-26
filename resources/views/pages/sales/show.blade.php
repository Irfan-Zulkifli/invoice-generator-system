@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="invoice-title">
                        <div class="d-flex align-items-start">
                            <div class="flex-grow-1">
                                <h4 class="card-title mb-4">Sale #{{ $sale->id }}</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="mb-4">
                                    {{-- Using your custom Enum methods here! --}}
                                    <span class="badge bg-{{ $sale->status->color() }} font-size-14">
                                        {{ ucwords($sale->status->label()) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6 mt-3">
                            <address>
                                <strong>Billed To:</strong><br>
                                {{ $sale->buyer->name }}<br>
                                @if($sale->buyer->email)
                                    {{ $sale->buyer->email }}<br>
                                @endif
                                @if($sale->buyer->phone)
                                    {{ $sale->buyer->phone }}<br>
                                @endif
                                @if($sale->buyer->address)
                                    {{ $sale->buyer->address }}
                                @endif
                            </address>
                        </div>
                        <div class="col-sm-6 mt-3 text-sm-end">
                            <address>
                                <strong>Order Date:</strong><br>
                                {{ $sale->created_at->format('d M, Y') }}<br><br>
                                <strong>Payment Due:</strong><br>
                                {{ \Carbon\Carbon::parse($sale->due_date)->format('d M, Y') }}
                            </address>
                        </div>
                    </div>
                    <div class="py-2 mt-3">
                        <h3 class="font-size-15 fw-bold">Order Summary</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-nowrap align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 70px;">No.</th>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th class="text-end" style="width: 120px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $grandTotal = 0; @endphp
                                
                                @foreach($sale->products as $product)
                                    @php 
                                        // Calculate subtotal for this row using pivot data
                                        $subtotal = $product->price * $product->pivot->quantity; 
                                        $grandTotal += $subtotal;
                                    @endphp
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>
                                            <h5 class="font-size-15 mb-1">{{ $product->name }}</h5>
                                        </td>
                                        <td>RM {{ number_format($product->price, 2) }}</td>
                                        <td>{{ $product->pivot->quantity }}</td>
                                        <td class="text-end">RM {{ number_format($subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                                
                                <tr>
                                    <th scope="row" colspan="4" class="text-end border-0">Sub Total</th>
                                    <td class="text-end border-0">RM {{ number_format($grandTotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="4" class="text-end border-0 fw-bold">Grand Total</th>
                                    <td class="text-end border-0 fw-bold">RM {{ number_format($grandTotal, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-print-none mt-4">
                        <div class="float-end">
                            <a href="javascript:window.print()" class="btn btn-success waves-effect waves-light me-1">
                                <i class="fa fa-print"></i> Print
                            </a>
                            <a href="{{ route('sales.payments', $sale) }}" class="btn btn-warning w-md waves-effect waves-light">
                                <i class="bx bx-money"></i>
                                See Payments
                            </a>
                            @if ($sale->status->label() == 'unpaid')
                                <a href="{{ route('sales.edit', $sale) }}" class="btn btn-primary w-md waves-effect waves-light">
                                    Edit Sale
                                </a>
                            @endif
                            @if ($sale->status->label() == 'paid')
                                <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-success w-md waves-effect waves-light">
                                    Receipt
                                </a>
                            @endif
                        </div>
                        <a href="{{ route('sales.index') }}" class="btn btn-secondary w-md waves-effect waves-light">
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 d-print-none">
            <div class="card">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h4 class="card-title mb-0">Payment History</h4>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        {{-- Removed table-light header, made table borderless --}}
                        <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                            <tbody>
                                {{-- Changed to forelse to handle empty states gracefully --}}
                                @forelse ($paymentsTable as $pay)
                                    <tr class="border-bottom">
                                        <td>
                                            <h5 class="font-size-14 mb-1 fw-bold text-dark">
                                                RM {{ number_format($pay->amount, 2) }}
                                            </h5>
                                            <span class="text-muted font-size-13">
                                                <i class="bx bx-calendar me-1"></i> 
                                                {{ \Carbon\Carbon::parse($pay->payment_date)->format('d M Y') }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-success-subtle text-success font-size-12 text-capitalize px-2 py-1">
                                                {{ str_replace('_', ' ', $pay->payment_method) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center text-muted py-4">
                                            <i class="bx bx-info-circle mb-2 font-size-20"></i><br>
                                            No payments recorded.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Only show the pagination container if there are enough pages --}}
                    @if($paymentsTable->hasPages())
                        <div class="mt-4 pt-3 d-flex justify-content-center">
                            {{ $paymentsTable->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')

@endsection