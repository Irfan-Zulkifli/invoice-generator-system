@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-12">
            @include('components.date-filter')
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">Total Customers</p>
                            <h4 class="mb-0">{{ $totalCustomers }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                <span class="avatar-title">
                                    <i class="bx bx-user font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">Total Products</p>
                            <h4 class="mb-0">{{ $totalProducts }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-primary">
                                    <i class="bx bx-box font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">Total Sales</p>
                            <h4 class="mb-0">{{ $totalSales }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-primary">
                                    <i class="bx bx-receipt font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">Total Revenue</p>
                            <h4 class="mb-0">RM {{ number_format($totalRevenue, 2) }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                <span class="avatar-title rounded-circle bg-primary">
                                    <i class="bx bx-dollar font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Recent Sales</h4>
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="align-middle">Sale ID</th>
                                    <th class="align-middle">Customer</th>
                                    <th class="align-middle">Date</th>
                                    <th class="align-middle">Total Amount</th>
                                    <th class="align-middle">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentSales as $sale)
                                    <tr>
                                        <td><a href="{{ route('sales.show', $sale->id) }}" class="text-body fw-bold">#{{ $sale->id }}</a> </td>
                                        <td>{{ $sale->buyer->name ?? 'N/A' }}</td>
                                        <td>
                                            {{ $sale->created_at->format('d M Y') }}
                                        </td>
                                        <td>
                                            RM {{ number_format($sale->total_price, 2) }}
                                        </td>
                                        <td>
                                            @if ($sale->status->value == 'completed')
                                                <span class="badge badge-pill badge-soft-success font-size-11">{{ ucfirst($sale->status->label()) }}</span>
                                            @elseif ($sale->status->value == 'pending')
                                                <span class="badge badge-pill badge-soft-warning font-size-11">{{ ucfirst($sale->status->label()) }}</span>
                                            @else
                                                <span class="badge badge-pill badge-soft-danger font-size-11">{{ ucfirst($sale->status->label()) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No recent sales found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Set date filter values from query parameters if they exist
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('start_date')) {
            $('#start_date').val(urlParams.get('start_date'));
        }
        if (urlParams.has('end_date')) {
            $('#end_date').val(urlParams.get('end_date'));
        }

        $('#btn-filter').on('click', function() {
            let startDate = $('#start_date').val();
            let endDate = $('#end_date').val();
            let url = "{{ route('dashboard') }}";
            let params = [];
            
            if (startDate) params.push(`start_date=${startDate}`);
            if (endDate) params.push(`end_date=${endDate}`);
            
            if (params.length > 0) {
                url += '?' + params.join('&');
            }
            window.location.href = url;
        });

        $('#btn-reset').on('click', function() {
            window.location.href = "{{ route('dashboard') }}";
        });
    });
</script>
@endsection