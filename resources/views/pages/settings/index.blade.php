@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-xl-8 mx-auto">
        <div class="card">
            <div class="card-body p-4">
                <div class="card-title mb-4">
                    Edit Setting
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="alert alert-primary">
                    The custom logo and icon will be used inside the invoice and receipt generated. If 
                    no logo and icon provided, none will be displayed in both invoice and receipt.
                </div>
                
                <form action="{{ $action }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @if (!$settingIsNull)
                        @method('PUT')
                    @endif

                    <h5 class="font-size-14 mb-3"><i class="bx bxs-cog me-2"></i>Setting Logo & Icon</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="custom_logo" class="form-label">Custom Logo</label>
                                <input class="form-control @error('custom_logo') is-invalid @enderror mb-2" type="file" id="custom_logo" name="custom_logo">
                                @error('custom_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @if ($isLogoExists)
                                    <div class="form-check form-check-primary mb-3">
                                        <input class="form-check-input" type="checkbox" id="deleteLogo" name="deleteLogo">
                                        <label class="form-check-label" for="deleteLogo">
                                            Delete Current Logo
                                        </label>
                                    </div>

                                    <div class="mb-2">
                                        <span class="form-label text-muted d-block fs-6">Current Logo Preview</span>
                                        <div class="p-2 border rounded bg-light d-inline-block">
                                            <img src="{{ asset('storage/' . $setting->custom_logo) }}" 
                                                alt="Current Custom Logo" 
                                                class="img-fluid rounded shadow-sm bg-white" 
                                                style="max-height: 120px; object-fit: contain;">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="custom_icon" class="form-label">Custom Icon</label>
                                <input class="form-control @error('custom_icon') is-invalid @enderror mb-2" type="file" id="custom_icon" name="custom_icon">
                                @error('custom_icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @if ($isIconExists)
                                    <div class="form-check form-check-primary mb-3">
                                        <input class="form-check-input" type="checkbox" id="deleteIcon" name="deleteIcon">
                                        <label class="form-check-label" for="deleteIcon">
                                            Delete Current Icon
                                        </label>
                                    </div>

                                    <div class="mb-2">
                                        <span class="form-label text-muted d-block fs-6">Current Icon Preview</span>
                                        <div class="p-2 border rounded bg-light d-inline-block">
                                            <img src="{{ asset('storage/' . $setting->custom_icon) }}" 
                                                alt="Current Custom Icon" 
                                                class="img-fluid rounded shadow-sm bg-white" 
                                                style="max-height: 120px; object-fit: contain;">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary w-md waves-effect waves-light">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection