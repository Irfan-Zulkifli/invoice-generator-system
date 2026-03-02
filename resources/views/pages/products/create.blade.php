@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Product Information</h4>

                    <form method="POST"
                        action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}">
                        @csrf
                        @if (isset($product))
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="formrow-firstname-input" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="formrow-firstname-input"
                                        placeholder="Enter Product Name" name="name" value="{{ old('name', $product->name) }}">
                                </div>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="formrow-password-input" class="form-label">Price</label>
                                    <input type="number" class="form-control" id="formrow-password-input"
                                        placeholder="Enter Price" name="price" value="{{ old('price', $product->price) }}">
                                </div>
                                @error('price')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="row">
                            <div class="mb-3">
                                <label for="formrow-email-input" class="form-label">Description</label>
                                <input type="text" class="form-control" id="formrow-email-input"
                                    placeholder="Enter Product Description" name="description" value="{{ old('description', $product->description) }}">
                                @error('description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div>
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
