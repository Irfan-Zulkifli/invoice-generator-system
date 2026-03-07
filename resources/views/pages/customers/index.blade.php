@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Customers List</h4>
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
@endsection
