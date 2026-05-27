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
            
            {{-- 1. PAYMENT HISTORY CARD --}}
            <div class="card">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h4 class="card-title mb-0">Payment History</h4>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                            <tbody>
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

                    @if($paymentsTable->hasPages())
                        <div class="mt-4 pt-3 d-flex justify-content-center">
                            {{ $paymentsTable->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h4 class="card-title mb-0">Activity Log</h4>
                </div>
                
                <div class="card-body" data-simplebar style="max-height: 400px;">
                    <ul class="verti-timeline list-unstyled mb-0">
                        @forelse($auditLogs as $log)
                            <li class="event-list {{ $loop->last ? 'mb-0' : '' }}">
                                <div class="event-timeline-dot">
                                    <i class="bx bx-right-arrow-circle text-primary"></i>
                                </div>
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <h5 class="font-size-13 text-muted mb-0">
                                            {{ $log->created_at->format('d M') }}
                                        </h5>
                                        <small class="text-muted">{{ $log->created_at->format('h:i A') }}</small>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div>
                                            <p class="text-muted mb-1 font-size-13">
                                                <span class="fw-bold text-dark">{{ $log->causer->name ?? 'System' }}</span>
                                                
                                                @if(in_array($log->description, ['created', 'updated', 'deleted']))
                                                    {{ $log->description }} the {{ strtolower(class_basename($log->subject_type)) }}.
                                                @else
                                                    {{ $log->description }}
                                                @endif
                                            </p>
                                            
                                            {{-- Attribute changes box --}}
                                            @if(isset($log->properties) && count($log->properties) > 0)
                                                <div class="mt-2 bg-light p-2 rounded text-muted font-size-12">
                                                    
                                                    {{-- 1. UPDATED: Shows Old -> New --}}
                                                    @if(isset($log->properties['old']) && isset($log->properties['attributes']))
                                                        @foreach($log->properties['attributes'] as $key => $newValue)
                                                            @if(isset($log->properties['old'][$key]) && $log->properties['old'][$key] != $newValue)
                                                                <div class="mb-1">
                                                                    <span class="fw-semibold text-dark">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span> 
                                                                    <span class="text-danger text-decoration-line-through">{{ $log->properties['old'][$key] ?? 'empty' }}</span> 
                                                                    <i class="bx bx-right-arrow-alt mx-1"></i>
                                                                    <span class="text-success">{{ $newValue ?? 'empty' }}</span>
                                                                </div>
                                                            @endif
                                                        @endforeach

                                                    {{-- 2. CREATED: Shows only New data --}}
                                                    @elseif(isset($log->properties['attributes']) && !isset($log->properties['old']))
                                                        @foreach($log->properties['attributes'] as $key => $value)
                                                            <div class="mb-1">
                                                                <span class="fw-semibold text-dark">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span> 
                                                                <span class="text-success">{{ $value ?? 'empty' }}</span>
                                                            </div>
                                                        @endforeach

                                                    {{-- 3. DELETED: Shows only Old data --}}
                                                    @elseif(isset($log->properties['old']) && !isset($log->properties['attributes']))
                                                        @foreach($log->properties['old'] as $key => $value)
                                                            <div class="mb-1">
                                                                <span class="fw-semibold text-dark">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span> 
                                                                <span class="text-danger text-decoration-line-through">{{ $value ?? 'empty' }}</span>
                                                            </div>
                                                        @endforeach
                                                    @endif

                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="text-muted text-center py-4">
                                <i class="bx bx-history mb-2 font-size-20"></i><br>
                                No activity recorded yet.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>
        
    </div>
@endsection
@section('scripts')

@endsection