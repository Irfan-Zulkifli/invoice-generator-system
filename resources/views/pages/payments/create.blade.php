@extends('layouts.app')

@section('content')
    @php
        $isShow = isset($isView) && $isView;
    @endphp
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bx bx-money me-2 text-primary"></i>Payment Details
                </h5>
                @if ($isShow)
                    <a href="{{ route('payments.edit', $payment) }}" class="btn btn-primary btn-xl waves-effect waves-light">
                        <i class="bx bx-pencil me-1"></i> Edit
                    </a>
                @endif
            </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ $payment->exists ? route('payments.update', $payment) : route('sales.payments.add', $sale) }}">
                        @csrf
                        @if ($payment->exists)
                            @method('PUT')
                        @endif

                        <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                        
                        <div class="row">
                            {{-- Payment Date --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="payment_date" class="form-label">Payment Date</label>
                                    
                                    <div class="input-group" id="datepicker1">
                                        <input type="text" 
                                            class="form-control @error('payment_date') is-invalid @enderror" 
                                            name="payment_date" 
                                            placeholder="dd/mm/yyyy"
                                            {{-- Tukar format value kepada d/m/Y supaya padan dengan datepicker --}}
                                            value="{{ old('payment_date', $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') : date('d/m/Y')) }}"
                                            data-date-format="dd/mm/yyyy" 
                                            data-date-container="#datepicker1" 
                                            data-provide="datepicker"
                                            data-date-autoclose="true"
                                            {{ $isShow ? 'disabled' : '' }}>

                                        <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                        
                                        {{-- Mesej ralat diletakkan di bawah input group dan ditambah d-block --}}
                                        @error('payment_date')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="mb-4">
                                <label>Default Functionality</label>
                                <div class="input-group" id="datepicker1">
                                    <input type="text" class="form-control" placeholder="dd/mm/yyyy"
                                        data-date-format="dd/mm/yyyy" data-date-container='#datepicker1' data-provide="datepicker">

                                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                </div>
                            </div> --}}

                            {{-- Amount --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">RM</span>
                                        <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" 
                                            placeholder="0.00" name="amount" value="{{ old('amount', $payment->amount) }}" {{ $isShow ? 'disabled' : '' }}>
                                        @error('amount')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Payment Method --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" {{ $isShow ? 'disabled' : '' }}>
                                        <option value="">-- Select Method --</option>
                                        @php
                                            $methods = ['tunai' => 'Tunai (Cash)', 'pindahan_bank' => 'Pindahan Bank (Bank Transfer)', 'kad_kredit' => 'Kad Kredit (Credit Card)', 'cek' => 'Cek (Cheque)'];
                                        @endphp
                                        @foreach($methods as $value => $label)
                                            <option value="{{ $value }}" {{ old('payment_method', $payment->payment_method) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Reference Number --}}
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="reference_number" class="form-label">Reference Number <span class="text-muted fw-normal">(Optional)</span></label>
                                    <input type="text" class="form-control @error('reference_number') is-invalid @enderror" id="reference_number" 
                                        placeholder="e.g. Transaction ID, Cheque Number" name="reference_number" value="{{ old('reference_number', $payment->reference_number) }}" {{ $isShow ? 'disabled' : '' }}>
                                    @error('reference_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Notes --}}
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label for="notes" class="form-label">Notes <span class="text-muted fw-normal">(Optional)</span></label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" rows="3" 
                                        placeholder="Add any extra details regarding this payment..." name="notes" {{ $isShow ? 'disabled' : ''}}>{{ old('notes', $payment->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('sales.payments', $sale) }}" class="btn btn-secondary waves-effect waves-light">
                                <i class="bx bx-arrow-back me-1"></i> Back to List
                            </a>
                            @if (!($isShow))
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    <i class="bx bx-save me-1"></i> {{ $payment->exists ? 'Update Payment' : 'Save Payment' }}
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection