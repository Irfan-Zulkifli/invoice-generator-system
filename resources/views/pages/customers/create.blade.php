@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Product Information</h4>

                    <form method="POST"
                        action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}">
                        @csrf
                        @if ($customer->exists)
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mt-3">
                                    <label for="name" class="form-label">Customer Name</label>
                                    <input type="text" class="form-control" id="name"
                                        placeholder="Enter Customer Name" name="name" value="{{ old('name', $customer->name) }}" required>
                                </div>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="mt-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email"
                                        placeholder="Enter Email" name="email" value="{{ old('email', $customer->email) }}">
                                </div>
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="mt-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone"
                                        placeholder="Enter Phone Num" name="phone" value="{{ old('phone', $customer->phone) }}">
                                </div>
                                @error('phone')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="mt-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="adderss"
                                        placeholder="Enter Customer Address" name="address" value="{{ old('address', $customer->address) }}">
                                </div>
                                @error('address')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary w-md">Submit</button>
                        </div>
                    </form>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
@endsection
