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
                                            @if ($sale->status->label() == 'paid')
                                                <span class="badge badge-pill badge-soft-success font-size-11">{{ ucfirst($sale->status->label()) }}</span>
                                            @elseif ($sale->status->label() == 'partially_paid')
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

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Sales by month</h4>
                    
                    <div id="column_chart" data-colors='["--bs-success","--bs-primary", "--bs-danger"]' class="apex-charts" dir="ltr"></div>                                      
                </div>
            </div><!--end card-->
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Product sold by month</h4>
                    
                    <div id="product_chart" data-colors='["--bs-success","--bs-primary", "--bs-danger"]' class="apex-charts" dir="ltr"></div>                                      
                </div>
            </div><!--end card-->
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Revenue by month</h4>
                    
                    <div id="revenue-chart" data-colors='["--bs-success","--bs-primary", "--bs-danger"]' class="apex-charts" dir="ltr"></div>                                      
                </div>
            </div><!--end card-->
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
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

        var options = {
            series: [
                {
                    name: 'Sales',
                    data: @json($monthCount)
                }
            ],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { 
                    show: false // Hides the hamburger menu for a cleaner dashboard look
                }
            },
            // NEW: Specifically shapes the columns!
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%', // Makes the bars slightly slimmer and more elegant
                    borderRadius: 4     // Gives the bars modern, slightly rounded tops
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: @json($monthName),
                axisBorder: { show: false }, // Removes the heavy black line at the bottom
                axisTicks: { show: false }   // Removes the tiny notches on the x-axis
            },
            yaxis: {
                title: {
                    text: 'Sales Count',
                    style: {
                        fontWeight: '500'
                    }
                },
            },
            fill: {
                opacity: 1
            },
            colors: ['#556ee6'],
            
            // NEW: Styles the background grid lines to match Bootstrap's light borders
            grid: {
                borderColor: '#f1f1f1', 
                strokeDashArray: 3 // Makes the grid lines slightly dotted/dashed
            },
            // NEW: Customizes the hover box
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " Total Sales"; // Adds nice text when they hover over a bar
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector('#column_chart'), options)
        chart.render();

        var optionProduct = {
            series: @json($productSeries),
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { 
                    show: false // Hides the hamburger menu for a cleaner dashboard look
                }
            },
            // NEW: Specifically shapes the columns!
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%', // Makes the bars slightly slimmer and more elegant
                    borderRadius: 4     // Gives the bars modern, slightly rounded tops
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: @json($monthName),
                axisBorder: { show: false }, // Removes the heavy black line at the bottom
                axisTicks: { show: false }   // Removes the tiny notches on the x-axis
            },
            yaxis: {
                title: {
                    text: 'Product Count',
                    style: {
                        fontWeight: '500'
                    }
                },
            },
            fill: {
                opacity: 1
            },
            colors: ['#556ee6', '#34c38f', '#f1b44c', '#f46a6a', '#50a5f1'],            
            // NEW: Styles the background grid lines to match Bootstrap's light borders
            grid: {
                borderColor: '#f1f1f1', 
                strokeDashArray: 3 // Makes the grid lines slightly dotted/dashed
            },
            // NEW: Customizes the hover box
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " Total Sales"; // Adds nice text when they hover over a bar
                    }
                }
            }  
        }

        var productChart = new ApexCharts(document.querySelector('#product_chart'), optionProduct);
        productChart.render();

        var lineOptions = {
            series: [
                {
                    name: 'Revenue',
                    data: @json($revenueMonthly), 
                },
            ],
            chart: {
                height: 350,
                type: 'line',
                zoom: { enabled: false },
                toolbar: { show: false }
            },
            colors: ['#556ee6'],
            dataLabels: {
                enabled: false,
            },
            stroke: {
                width: 3,
                curve: 'straight',
            },
            
            // 👇 1. ADD MARKERS TO FIX THE INVISIBLE 1-POINT CHART 👇
            markers: {
                size: 6, // Forces a visible dot on the line
                colors: ['#556ee6'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: {
                    size: 8
                }
            },
            
            // 👇 2. ADD TOOLTIP TO FORMAT AS CURRENCY 👇
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "RM " + parseFloat(val).toFixed(2);
                    }
                }
            },
            
            grid: {
                borderColor: '#f1f1f1',
                row: {
                    colors: ['transparent', 'transparent'],
                    opacity: 0.5,
                },
            },
            yaxis: {
                title: {
                    text: 'Revenue (RM)',
                    style: {
                        fontWeight: '500'
                    }
                },
            },
            xaxis: {
                categories: @json($monthName), 
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
        };

        // 👇 3. CHANGE THE VARIABLE NAME SO IT DOES NOT CRASH YOUR BAR CHARTS 👇
        var revenueChart = new ApexCharts(document.querySelector('#revenue-chart'), lineOptions);
        revenueChart.render();
    });
</script>
@endsection