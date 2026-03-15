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
                            <div class="col-md-6" id="templateArea">

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
                                <select class="form-control select2" name="customer_id">
                                    <option value="">Select Customer</option>
                                    @foreach ($existingCustomers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </template>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
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
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#select2').select2();
        })
    </script>
@endsection
