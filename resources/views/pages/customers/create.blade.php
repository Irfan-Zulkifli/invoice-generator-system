@extends('layouts.app')

@section('content')
    {{-- <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-4">
                <h4 class="mb-sm-0 font-size-18">Customer Information</h4>
            </div>
        </div>
    </div> --}}

    <div class="row">
        <div class="col-xl-8 mx-auto">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0"><i class="bx bx-user me-2 text-primary"></i>Customer Details</h5>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}">
                        @csrf
                        @if ($customer->exists)
                            @method('PUT')
                        @endif
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Customer Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                        placeholder="Enter Customer Name" name="name" value="{{ old('name', $customer->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-muted fw-normal">(Optional)</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                        placeholder="Enter Email" name="email" value="{{ old('email', $customer->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number <span class="text-muted fw-normal">(Optional)</span></label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                        placeholder="Enter Phone Number" name="phone" value="{{ old('phone', $customer->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label for="address" class="form-label">Address <span class="text-muted fw-normal">(Optional)</span></label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" rows="3"
                                        placeholder="Enter Customer Address" name="address">{{ old('address', $customer->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center">
                            {{-- Optional: Add a back button if you have an index route --}}
                            <a href="{{ route('customers.index') }}" class="btn btn-secondary waves-effect waves-light">
                                <i class="bx bx-arrow-back me-1"></i> Back to List
                            </a>
                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                <i class="bx bx-save me-1"></i> {{ $customer->exists ? 'Update Customer' : 'Save Customer' }}
                            </button>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div>
@endsection