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
                                    <div class="d-flex align-items-center gap-4 mt-2">
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
                                <div class="col-md-6">
                                    <label for=""></label>
                                </div>
                            </div>
                            <template id="newCustomerTemplate">
                                <input type="text" class="form-control" name="customer_name">
                            </template>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function changeCustomerInput(event) {
            console.log(event.target.value);
        }

        const radios = document.getElementsByName('formRadios');

        for (let i = 0; i < radios.length; i++) {
            radios[i].addEventListener('change', changeCustomerInput);
        }
    </script>
@endsection
