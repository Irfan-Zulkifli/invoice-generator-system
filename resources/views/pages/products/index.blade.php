@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-12">
            @include('components.date-filter')
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Products List</h4>
                    {{-- Table container --}}
                    <div class="table-responsive">
                        {{ $dataTable->table(['class' => 'table table-bordered dt-responsive nowrap w-100 yajra-datatable']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

        {{-- add modal --}}
    <div class="modal fade" id="addStockModal" tabindex="-1" aria-labelledby="addStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title" id="addStockModalLabel">Add Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addStockForm" method="POST" action="">
                    @csrf
                    <div class="modal-body">
                        {{-- Hidden input to store which product we are updating --}}
                        <input type="hidden" name="product_id" id="add_product_id">
                        
                        <div class="mb-3">
                            <label for="add_quantity" class="form-label">Quantity to Add <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="add_quantity" name="quantity" min="1" required placeholder="e.g., 50">
                        </div>
                        
                        <div class="mb-3">
                            <label for="add_notes" class="form-label">Reference Notes</label>
                            <textarea class="form-control" id="add_notes" name="notes" rows="3" placeholder="e.g., Supplier delivery #1234"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success waves-effect waves-light">
                            <i class="bx bx-plus-circle align-middle me-1"></i> Confirm Addition
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Decrease Modal --}}
    <div class="modal fade" id="decreaseStockModal" tabindex="-1" aria-labelledby="decreaseStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title" id="decreaseStockModalLabel">Decrease Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="decreaseStockForm" method="POST" action="">
                    @csrf
                    <div class="modal-body">
                        {{-- Hidden input to store which product we are updating --}}
                        <input type="hidden" name="product_id" id="decrease_product_id">
                        
                        <div class="mb-3">
                            <label for="decrease_quantity" class="form-label">Quantity to Decrease <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="decrease_quantity" name="quantity" min="1" required placeholder="e.g., 5">
                        </div>
                        
                        <div class="mb-3">
                            <label for="decrease_notes" class="form-label">Reason / Notes</label>
                            <textarea class="form-control" id="decrease_notes" name="notes" rows="3" placeholder="e.g., Damaged goods, expired, internal use, etc."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger waves-effect waves-light">
                            <i class="bx bx-minus-circle align-middle me-1"></i> Confirm Decrease
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    {!! $dataTable->scripts() !!}
    <script>
        $(document).ready(function() {
            $('#btn-filter').on('click', function() {
                let tableId = Object.keys(window.LaravelDataTables)[0];
                window.LaravelDataTables[tableId].draw();
            });
            $('#btn-reset').on('click', function() {
                $('#start_date').val('');
                $('#end_date').val('');
                let tableId = Object.keys(window.LaravelDataTables)[0];
                window.LaravelDataTables[tableId].draw();
            });
        });

        function deleteProduct(id) {
            Swal.fire({
                title: "Are you sure want to delete this?",
                text: "You won't be able to revert this.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonClass: 'btn btn-primary w-xs me-2 mt-2',
                cancelButtonClass: 'btn btn-danger w-xs mt-2',
                confirmButtonText: "Yes, delete it!",
                showCloseButton: true
            }).then(function(result) {
                if (result.isConfirmed) {
                    let formElement = document.getElementById(`delete-form-${id}`);
                    let formData = new FormData(formElement);
                    let actionUrl = formElement.getAttribute('action');

                    fetch(actionUrl, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonClass: 'btn btn-primary w-xs mt-2',
                                }).then(() => {
                                    window.location.href = data.redirect;
                                })
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        });
                }
            })
        }

        $('#addStockModal').on('show.bs.modal', function (event) {

            let buttonTarget = $(event.relatedTarget);

            let labelTitle = $('#addStockModalLabel');
            labelTitle.text('Add Stock');
            labelTitle.text(labelTitle.text() + ': #' + buttonTarget.data('id'));

            let productId = buttonTarget.data('id');

            $('#add_product_id').val(productId);

            let formAction = `/products/${productId}/add-stock`;

            $('#addStockForm').attr('action', formAction);
        })

        $('#decreaseStockModal').on('show.bs.modal', function (event) {
            let buttonTarget = $(event.relatedTarget);

            let labelTitle = $('#decreaseStockModalLabel');
            labelTitle.text('Decrease Stock');
            labelTitle.text(labelTitle.text() + ': #' + buttonTarget.data('id'));

            let productId = buttonTarget.data('id');

            $('#decrease_product_id').val(productId);

            let formAction = `/products/${productId}/decrease-stock`;

            $('#decreaseStockForm').attr('action', formAction);
        })
    </script>
@endsection
