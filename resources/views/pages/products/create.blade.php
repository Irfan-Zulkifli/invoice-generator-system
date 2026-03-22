@extends('layouts.app')

@section('content')
    {{-- <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-4">
                <h4 class="mb-sm-0 font-size-18">Product Information</h4>
            </div>
        </div>
    </div> --}}

    <div class="row">
        <div class="col-xl-8 mx-auto">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0"><i class="bx bx-box me-2 text-primary"></i>Product Details</h5>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}">
                        @csrf
                        @if ($product->exists)
                            @method('PUT')
                        @endif
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Product Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" 
                                        placeholder="Enter Product Name" name="name" value="{{ old('name', $product->name) }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">RM</span>
                                        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" 
                                            placeholder="0.00" name="price" value="{{ old('price', $product->price) }}">
                                        @error('price')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label for="description" class="form-label">Description <span class="text-muted fw-normal">(Optional)</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" rows="4" 
                                        placeholder="Enter Product Description" name="description">{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center">
                            {{-- Assuming you have a products.index route to go back to --}}
                            <a href="{{ route('products.index') }}" class="btn btn-secondary waves-effect waves-light">
                                <i class="bx bx-arrow-back me-1"></i> Back to List
                            </a>
                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                <i class="bx bx-save me-1"></i> {{ $product->exists ? 'Update Product' : 'Save Product' }}
                            </button>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div>
@endsection