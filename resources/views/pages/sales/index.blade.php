@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Sales List</h4>
                    {{-- Table container --}}
                    <div class="table-responsive">
                        {{ $dataTable->table(['class' => 'table table-bordered dt-responsive nowrap w-100 yajra-datatable']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    {!! $dataTable->scripts() !!}
    <script>
        function deleteCustomer(id) {
            Swal.fire({
                title: "Are you sure to delete this?",
                text: "You won't be able to revert this!",
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
            });
        }
    </script>
@endsection
