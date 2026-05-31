@extends('layouts.app')

@section('content')
    {{-- <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-4">
                <h4 class="mb-sm-0 font-size-18">Sale Information</h4>
            </div>
        </div>
    </div> --}}

    <form action="{{ $sale->exists ? route('sales.update', $sale) : route('sales.store') }}" method="post">
        @csrf
        @if ($sale->exists)
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0"><i class="bx bx-user-circle me-2 text-primary"></i>Customer Details</h5>
                    </div>
                    <div class="card-body">

                        {{-- allow change customer if: sale does not exists (means new sale and not edit) OR sale exists but status is unpaid --}}
                        {{-- Otherwise, the form will show the customer details with disabled input --}}
                        @if (!$sale->exists || ($sale->exists && $sale->status->label() == 'unpaid'))
                            <div class="mb-4 p-3 border rounded bg-light">
                                <label class="form-label mb-2 fw-bold">Existing Customer or Not?</label>
                                <div class="d-flex align-items-center gap-4 mt-1">
                                    <div class="form-check form-radio-primary mb-0">
                                        <input class="form-check-input" type="radio" name="formRadios" id="yes"
                                            value="yes" checked>
                                        <label class="form-check-label" for="yes">Yes</label>
                                    </div>
                                    <div class="form-check form-radio-primary mb-0">
                                        <input class="form-check-input" type="radio" name="formRadios" id="no"
                                            value="no">
                                        <label class="form-check-label" for="no">No</label>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="name" class="form-label">Customer Name</label>
                                    <input type="text" class="form-control" id="name"
                                        placeholder="Enter Customer Name" name="name" value="{{ old('name', '') }}"
                                        required>
                                    @error('name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="email" class="form-label">Email <span
                                            class="text-muted fw-normal">(Optional)</span></label>
                                    <input type="email" class="form-control" id="email" placeholder="Enter Email"
                                        name="email" value="{{ old('email', '') }}">
                                    @error('email')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" placeholder="Enter Phone Num"
                                        name="phone" value="{{ old('phone', '') }}">
                                    @error('phone')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-2">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" rows="3" placeholder="Enter Customer Address" name="address">{{ old('address', '') }}</textarea>
                                    @error('address')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif

                        <div id="templateArea"></div>

                        <template id="newCustomerTemplate">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="name" class="form-label">Customer Name</label>
                                    <input type="text" class="form-control" id="name"
                                        placeholder="Enter Customer Name" name="name" value="{{ old('name', '') }}"
                                        required>
                                    @error('name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="email" class="form-label">Email <span
                                            class="text-muted fw-normal">(Optional)</span></label>
                                    <input type="email" class="form-control" id="email" placeholder="Enter Email"
                                        name="email" value="{{ old('email', '') }}">
                                    @error('email')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" placeholder="Enter Phone Num"
                                        name="phone" value="{{ old('phone', '') }}">
                                    @error('phone')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-2">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" rows="3" placeholder="Enter Customer Address" name="address">{{ old('address', '') }}</textarea>
                                    @error('address')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </template>

                        <template id="existingCustomerTemplate">
                            <div class="mb-3">
                                <label class="form-label">Select Customer</label>
                                <select class="form-control select2_customer @error('customer_id') is-invalid @enderror"
                                    name="customer_id">
                                    <option value="">Select Customer</option>
                                    @foreach ($existingCustomers as $customer)
                                        <option value="{{ $customer->id }}"
                                            {{ ($sale->exists ? old('customer_id', $sale->buyer->id) : old('customer_id')) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} - {{ $customer->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div
                        class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0"><i class="bx bx-cart me-2 text-primary"></i>Order Details</h5>
                        <button type="button" class="btn btn-sm btn-soft-primary waves-effect waves-light"
                            onclick="addRowButton()">
                            <i class="bx bx-plus me-1"></i> Add Product
                        </button>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive border rounded mb-4">
                            <table class="table table-hover table-nowrap align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product Selection</th>
                                        <th style="width: 150px;">Total Unit</th>
                                        <th style="width: 80px;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($sale->exists)
                                        @foreach ($sale->products as $index => $existingProduct)
                                            <tr>
                                                <td>
                                                    <select
                                                        class="form-control select2 @error("product_id.$index") is-invalid @enderror"
                                                        name="product_id[]">
                                                        <option value="">Select Product</option>
                                                        @foreach ($products as $product)
                                                            <option value="{{ $product->id }}"
                                                                {{ $existingProduct->id == $product->id ? 'selected' : '' }}>
                                                                {{ $product->name }} - {{ $product->price }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error("product_id.$index")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="number" name="quantity[]"
                                                        value="{{ $existingProduct->pivot->quantity }}"
                                                        class="form-control @error("quantity.$index") is-invalid @enderror">
                                                    @error("quantity.$index")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text stock-indicator text-muted d-block mt-1" style="font-size: 0.75rem;">
                                                        Select a product to view stock
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                        class="btn btn-sm btn-soft-danger waves-effect waves-light"
                                                        onclick="deleteRowButton(event)">
                                                        <i class="bx bx-trash-alt font-size-16"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    @if (old('product_id'))
                                        @foreach (old('product_id') as $index => $oldProductId)
                                            <tr>
                                                <td>
                                                    <select
                                                        class="form-control select2 @error("product_id.$index") is-invalid @enderror"
                                                        name="product_id[]">
                                                        <option value="">Select Product</option>
                                                        @foreach ($products as $product)
                                                            <option value="{{ $product->id }}"
                                                                {{ $oldProductId == $product->id ? 'selected' : '' }}>
                                                                {{ $product->name }} - {{ $product->price }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error("product_id.$index")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="number" name="quantity[]"
                                                        value="{{ old("quantity.$index") }}"
                                                        class="form-control @error("quantity.$index") is-invalid @enderror">
                                                    @error("quantity.$index")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text stock-indicator text-muted d-block mt-1" style="font-size: 0.75rem;">
                                                        Select a product to view stock
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                        class="btn btn-sm btn-soft-danger waves-effect waves-light"
                                                        onclick="deleteRowButton(event)">
                                                        <i class="bx bx-trash-alt font-size-16"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        @error('product_id')
                            @if ($message == 'You are required to pick at least one product.')
                                <div class="text-danger mb-4"><i class="bx bx-error-circle me-1"></i>{{ $message }}
                                </div>
                            @endif
                        @enderror

                        <template id="table-row-template">
                            <tr>
                                <td>
                                    <select class="form-control select2 @error('product_id[]') is-invalid @enderror"
                                        name="product_id[]">
                                        <option value="">Select Product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} -
                                                {{ $product->price }}</option>
                                        @endforeach
                                    </select>
                                    @error('product_id[]')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="number" name="quantity[]" value=""
                                        class="form-control @error('quantity[]') is-invalid @enderror">
                                        
                                    <small class="form-text stock-indicator text-muted d-block mt-1" style="font-size: 0.75rem;">
                                        Select a product to view stock
                                    </small>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-soft-danger waves-effect waves-light"
                                        onclick="deleteRowButton()">
                                        <i class="bx bx-trash-alt font-size-16"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>

                        <hr class="my-4">

                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Payment Due Date</label>
                                <div class="input-group" id="datepicker2">
                                    <input type="text" class="form-control" name="due_date" placeholder="yyyy-mm-dd"
                                        data-date-format="yyyy-mm-dd" data-date-container="body"
                                        data-provide="datepicker" data-date-autoclose="true"
                                        value="{{ old('due_date', $sale->due_date ? \Carbon\Carbon::parse($sale->due_date)->format('Y-m-d') : '') }}">
                                    <span class="input-group-text bg-light"><i
                                            class="mdi mdi-calendar text-primary"></i></span>
                                </div>
                                @error('due_date')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 text-end mt-4 mt-md-0">
                                <button type="submit" class="btn btn-primary btn-lg waves-effect waves-light">
                                    <i class="bx bx-check-double me-1"></i> Save Sale Record
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>

    <style>
        /* Fix for Select2 validation red border */
        .is-invalid+.select2-container--default .select2-selection--single {
            border-color: #dc3545 !important;
        }

        .is-invalid+.select2-container--default.select2-container--focus .select2-selection--single {
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
                    $('.select2_customer').select2({
                        placeholder: "Select Customer",
                        allowClear: true,
                        width: '100%'
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
            tbody.appendChild(clone);

            let newSelect = tbody.querySelector('tr:last-child .select2');
            $(newSelect).select2({
                placeholder: "Select Product",
                allowClear: true,
                width: '100%'
            });

            syncProductDropdowns();
        }

        function deleteRowButton(event) {
            // Updated to handle both direct clicks and event passing
            let target = event ? event.target : window.event.target;
            let rowTarget = target.closest('tr');
            if (rowTarget) {
                rowTarget.remove();
            }
        }

        function getProductQuantity (productId, elem) {

            if (!productId) return;

            let baseRoute = "{{ route('products.get-product-quantity', '__ID__') }}";

            let fetchUrl = baseRoute.replace('__ID__', productId);

            fetch(fetchUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    let tdNow = elem.closest('td');
                    let tdNext = tdNow.next('td');
                    let inputQuantity = tdNext.find("input[name='quantity[]']");
                    let stockMessage = tdNext.find('.stock-indicator');

                    let available = data.product_quantity;

                    if (available <= 0) {
                        stockMessage.text('❌ Out of Stock')
                            .removeClass('text-muted text-success')
                            .addClass('text-danger fw-bold');
                        inputQuantity.attr({
                            'min': 0,
                            'max': 0,
                        });
                        
                    } else if (available <= 5) {
                        stockMessage.text(`⚠️ Only ${available} units left!`)
                            .removeClass('text-muted text-success')
                            .addClass('text-danger fw-bold');
                        inputQuantity.attr({
                            'min': 1,
                            'max': available,
                        });
                    } else {
                        stockMessage.text(`✨ ${available} units available`)
                                    .removeClass('text-muted text-danger fw-bold')
                                    .addClass('text-success');
                        inputQuantity.attr({
                            'min': 1,
                            'max': available,
                        });
                    }
                    
                    let currentProducts = @js($products);

                    // console.log(data.product_selected_id);

                    currentProducts = currentProducts.filter(prod => prod.id != data.product_selected_id);

                    console.log(currentProducts);
                    console.log(@js($products));
                    
                    // console.log(currentProducts);

                    
                })
                .catch(error => console.error('Fetch error:', error));
        }

        function syncProductDropdowns() {
            let chosenIds = [];

            $("select[name='product_id[]']").each(function () {
                let currentVal = $(this).val();
                if (currentVal) {
                    chosenIds.push(currentVal);
                }
            });

            $("select[name='product_id[]']").each(function () {
                // compare option kalau setiap option tu ade chosenIds, kita disable option tu
                // so skrg, loop option untuk setiap select
                let currentSelect = $(this);
                let activeSelection = currentSelect.val();

                currentSelect.find('option').each(function () {
                    let currentOption = $(this);
                    let optionVal = currentOption.val();

                    if (chosenIds.includes(optionVal) && optionVal !== activeSelection) {
                        currentOption.attr('disabled', 'disabled');
                        if (!currentOption.text().includes('(Already Selected)')) {
                            currentOption.text(currentOption.text() + ' (Already Selected)');
                        }
                    } else {
                        currentOption.removeAttr('disabled');
                        currentOption.text(currentOption.text().replace(' (Already Selected)', ''));
                    }
                })

                if (currentSelect.hasClass('select2-hidden-accessible')) {
                    currentSelect.select2({
                        placeholder: "Select Product",
                        allowClear: true,
                        width: '100%'
                    });
                }

            })
        }
        
    </script>
    <script>
        $(document).ready(function() {
            $('#select2').select2();
            $('tbody').on('change', "select[name='product_id[]']", function () {
                let selectedId = $(this).val();
            
                getProductQuantity(selectedId, $(this));

                syncProductDropdowns();
            });

            $('tbody select[name="product_id[]"]').each(function() {
                $(this).select2({
                    placeholder: "Select Product",
                    allowClear: true,
                    width: '100%'
                });
                if ($(this).val()) {
                    $(this).trigger('change');
                }
            });

            window.deleteRowButton = function (event) {
                let target = event ? event.target : window.event.target;
                let rowTarget = target.closest('tr');
                if (rowTarget) {
                    rowTarget.remove();
                    syncProductDropdowns();
                }
            }
        });
    </script>
@endsection
