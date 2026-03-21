@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        Sale Information
                    </h4>

                    <form action="{{ $sale->exists ? route('sales.update', $sale) : route('sales.store') }}" method="post">
                        @csrf
                        @if ($sale->exists)
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="">Existing Customer or Not?</label>
                                            <div class=" d-flex align-items-center justify-content-evenly gap-4 mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="formRadios" id="yes"
                                                        value="yes" checked>
                                                    <label class="form-check-label" for="yes">
                                                        Yes
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="formRadios" id="no"
                                                        value="no">
                                                    <label class="form-check-label" for="no">
                                                        No
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3" id="templateArea">
                                    
                                        
                                    </div>
                                    <template id="newCustomerTemplate">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mt-3">
                                                    <label for="name" class="form-label">Customer Name</label>
                                                    <input type="text" class="form-control" id="name"
                                                        placeholder="Enter Customer Name" name="name"
                                                        value="{{ old('name', '') }}" required>
                                                </div>
                                                @error('name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-12">
                                                <div class="mt-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" class="form-control" id="email"
                                                        placeholder="Enter Email" name="email" value="{{ old('email', '') }}">
                                                </div>
                                                @error('email')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-12">
                                                <div class="mt-3">
                                                    <label for="phone" class="form-label">Phone Number</label>
                                                    <input type="text" class="form-control" id="phone"
                                                        placeholder="Enter Phone Num" name="phone" value="{{ old('phone', '') }}">
                                                </div>
                                                @error('phone')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-12">
                                                <div class="mt-3">
                                                    <label for="address" class="form-label">Address</label>
                                                    <input type="text" class="form-control" id="adderss"
                                                        placeholder="Enter Customer Address" name="address"
                                                        value="{{ old('address', '') }}">
                                                </div>
                                                @error('address')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>
                                    </template>
                                    <template id="existingCustomerTemplate">
                                        <label class="form-label">Customer</label>
                                        <select class="form-control select2 @error('customer_id') is-invalid @enderror" name="customer_id">
                                            <option value="">Select Customer</option>
                                            @foreach ($existingCustomers as $customer)
                                                <option value="{{ $customer->id }}" {{ (old('customer_id') == $customer->id ? 'selected' : '') }}>{{ $customer->name }} - {{ $customer->email }}</option>
                                            @endforeach
                                        </select>
                                        @error('customer_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </template>
                                </div>
                                

                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="">Product</label>
                                            <div class="text-end mb-3">
                                                <button type="button" class="btn btn-primary waves-effect btn-label waves-light" onclick="addRowButton()"><i class="bx bx-plus-medical label-icon"></i> Add Product</button>
                                            </div>
                                            <div class="table-responsive table-bordered mb-3">
                                                <table class="table mb-0">
                                                    <thead>
                                                        <tr>
                                                            {{-- <th>#</th> --}}
                                                            <th>Product</th>
                                                            <th>Total Unit</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(old('product_id'))
                                                            @foreach(old('product_id') as $index => $oldProductId)
                                                                <tr>
                                                                    {{-- <th scope="row">{{ $loop->iteration }}</th> --}}
                                                                    <td>
                                                                        {{-- Check for errors on this specific row index (e.g., product_id.0) --}}
                                                                        <select class="form-control select2 @error("product_id.$index") is-invalid @enderror" name="product_id[]">
                                                                            <option value="">Select Product</option>
                                                                            @foreach ($products as $product)
                                                                                <option value="{{ $product->id }}" {{ $oldProductId == $product->id ? 'selected' : '' }}>
                                                                                    {{ $product->name }} - {{ $product->price }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error("product_id.$index")
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </td>
                                                                    <td>
                                                                        {{-- Get the matching old quantity, check for errors on quantity.$index --}}
                                                                        <input type="number" name="quantity[]" value="{{ old("quantity.$index") }}" class="form-control @error("quantity.$index") is-invalid @enderror" style="min-width: 80px;">
                                                                        @error("quantity.$index")
                                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-danger waves-effect waves-light" onclick="deleteRowButton(event)">
                                                                            <i class="bx bx-trash-alt"></i> 
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                                @error('product_id')
                                                    @if($message == 'You are required to pick at least one product.')
                                                        <div class="text-danger mt-2">{{ $message }}</div>
                                                    @endif
                                                @enderror
                                            </div>
                                            <template id="table-row-template">
                                                <tr>
                                                    {{-- <th scope="row"></th> --}}
                                                    <td>
                                                        <select class="form-control select2 @error('product_id[]') is-invalid @enderror" name="product_id[]">
                                                            <option value="">Select Product</option>
                                                            @foreach ($products as $product)
                                                                <option value="{{ $product->id }}">{{ $product->name }} - {{ $product->price }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('product_id[]')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" name="quantity[]" value="" class="form-control @error('quantity[]') is-invalid @enderror" style="min-width: 80px;">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger waves-effect waves-light" onclick="deleteRowButton()">
                                                            <i class="bx bx-trash-alt"></i> 
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                            {{-- <div class="mb-3">
                                                <label>Due date for payment completion</label>
                                                <div class="input-group" id="datepicker2">
                                                    <input type="text" class="form-control" placeholder="dd M, yyyy"
                                                        data-date-format="dd M, yyyy" data-date-container='#datepicker2' data-provide="datepicker"
                                                        data-date-autoclose="true">

                                                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                                </div>
                                            </div> --}}
                                            <div class="mb-3">
                                                <label>Due date for payment completion</label>
                                                <div class="input-group" id="datepicker2">
                                                    <input type="text" class="form-control" name="due_date" placeholder="yyyy-mm-dd"
                                                        data-date-format="yyyy-mm-dd" data-date-container="body" data-provide="datepicker"
                                                        data-date-autoclose="true" value="{{ old('due_date') }}">

                                                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                                </div>
                                                @error('due_date')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary w-md">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <style>
        /* Make the Select2 box red if the hidden select is invalid */
        .is-invalid + .select2-container--default .select2-selection--single {
            border-color: #dc3545 !important;
        }
        
        /* Optional: Add the red outline when the user clicks/focuses on it */
        .is-invalid + .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
        }
    </style>
@endsection
@section('scripts')
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery.repeater/jquery.repeater.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-repeater.int.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/libs/spectrum-colorpicker2/spectrum.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>
    <script src="{{ asset('assets/libs/tui-date-picker/tui-date-picker.min.js') }}"></script>
    <script>

        function getTemplate(value) {
            let targetElement = value == 'yes' ? document.querySelector('#existingCustomerTemplate') : document
                .querySelector('#newCustomerTemplate');
            return targetElement;
        }

        function changeCustomerInput(event) {
            let templateArea = document.getElementById('templateArea');
            templateArea.innerHTML = '';
            let targetElement = getTemplate(event.target.value);
            let templateContent = targetElement.content;
            let newItem = document.importNode(templateContent, true);
            templateArea.appendChild(newItem);
            if (event.target.value === 'yes') {
                $('.select2').select2({
                    placeholder: "Select Customer",
                    allowClear: true,
                    width: '100%' // Helps prevent weird resizing bugs in dynamic divs
                });
            }
        }

        const radios = document.getElementsByName('formRadios');

        for (let i = 0; i < radios.length; i++) {
            radios[i].addEventListener('change', changeCustomerInput);
        }

        document.addEventListener("DOMContentLoaded", function() {
            let checkedRadio = document.querySelector('input[name="formRadios"]:checked');
            if (checkedRadio) {
                // This dispatches a true native event, so event.target works perfectly
                checkedRadio.dispatchEvent(new Event('change'));
            }
            let tbody = document.querySelector('tbody');
            if (tbody.rows.length === 0) {
                addRowButton();
            }
        });

        function addRowButton() {
            let tbody = document.querySelector('tbody');
            let template = document.getElementById('table-row-template');
            let clone = template.content.cloneNode(true);
            // clone.querySelector('th').textContent = tbody.rows.length + 1;
            tbody.appendChild(clone);
            let newSelect = tbody.querySelector('tr:last-child .select2');
            $(newSelect).select2({
                placeholder: "Select Product",
                allowClear: true,
                width: '100%'
            });      
        }

        function deleteRowButton() {
            let rowTarget = event.target.closest('tr');
            rowTarget.remove();
        }
    </script>
    <script>
        $(document).ready(function() {
            $('#select2').select2();
        })
    </script>
@endsection
