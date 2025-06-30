@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- Total Revenue Card -->
            <div class="col-lg-3 col-md-6 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-dollar-circle bx-sm"></i>
                                </span>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Revenue</span>
                        <h3 class="card-title mb-2">RP{{ number_format($totalRevenue, 2) }}</h3>
                        <small class="text-success fw-semibold">
                            <i class="bx bx-up-arrow-alt"></i> {{ number_format($growthPercentage, 2) }}%
                        </small>
                    </div>
                    <div id="revenueGrowthChart" data-percentage="{{ $growthPercentage }}"></div>
                </div>
            </div>

            <!-- Total Orders Card -->
            <div class="col-lg-3 col-md-6 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="bx bx-shopping-bag bx-sm"></i>
                                </span>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Orders</span>
                        <h3 class="card-title mb-2">{{ number_format($totalOrders) }}</h3>
                        <small class="text-success fw-semibold">
                            <i class="bx bx-up-arrow-alt"></i>
                            {{ number_format(($todayOrders / $totalOrders) * 100, 2) }}% today
                        </small>
                    </div>
                </div>
            </div>

            <!-- Total Products Card -->
            <div class="col-lg-3 col-md-6 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="bx bx-package bx-sm"></i>
                                </span>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Products</span>
                        <h3 class="card-title mb-2">{{ number_format($totalProducts) }}</h3>
                    </div>
                </div>
            </div>

            <!-- Total Customers Card -->
            <div class="col-lg-3 col-md-6 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-user bx-sm"></i>
                                </span>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total Customers</span>
                        <h3 class="card-title mb-2">{{ number_format($totalCustomers) }}</h3>
                        <small class="text-success fw-semibold">
                            <i class="bx bx-up-arrow-alt"></i> {{ count($recentCustomers) }} new today
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Monthly Revenue Chart -->
            {{-- <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Monthly Revenue ({{ $currentYear }} vs {{ $previousYear }})</h5>
            <div class="dropdown">
                <button class="btn p-0" type="button" id="revenueReport" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="revenueReport">
                    <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                    <a class="dropdown-item" href="javascript:void(0);">Export</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="monthlyRevenueChart" data-current-year="{{ json_encode($monthlyRevenueCurrentYear) }}"
                data-previous-year="{{ json_encode($monthlyRevenuePreviousYear) }}"
                data-year-current="{{ $currentYear }}" data-year-previous="{{ $previousYear }}">
            </div>
        </div>
    </div>
</div> --}}
</div>

<div class="row">
    <!-- Top Selling Products -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Top Selling Products</h5>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="topProducts" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="topProducts">
                        <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                        <a class="dropdown-item" href="javascript:void(0);">Export</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($topProducts->isNotEmpty())
                <div id="topProductsChart" data-products="{{ json_encode($topProducts->pluck('name')->toArray()) }}"
                    data-quantities="{{ json_encode($topProducts->pluck('order_items_sum_quantity')->toArray()) }}">
                </div>
                @else
                <div class="text-center py-4">
                    <p class="text-muted">No sales data available</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Order Status Distribution -->
    <div class="col-lg-6 mb-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Order Status Distribution</h5>
            <div class="dropdown">
                <button class="btn p-0" type="button" id="orderStatus" data-bs-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orderStatus">
                    <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                    <a class="dropdown-item" href="javascript:void(0);">Export</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($orderStatuses->isNotEmpty())
                <div id="orderStatusChart"
                    data-statuses="{{ json_encode($orderStatuses->pluck('order_status')->toArray()) }}"
                    data-counts="{{ json_encode($orderStatuses->pluck('count')->toArray()) }}">
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-muted">No order status data available</p>
                </div>
            @endif
        </div>
    </div>
</div>
</div>

<div class="row">
    <!-- Recent Transactions -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Transactions</h5>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="transactionOptions" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionOptions">
                        <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                        <a class="dropdown-item" href="javascript:void(0);">Export</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach($recentOrders as $transaction)
                            <tr>
                                <td><strong>#{{ $transaction->id }}</strong></td>
                                <td>{{ $transaction->customer->name ?? 'Guest' }}</td>
                                <td>{{ $transaction->created_at->format('M d, Y') }}</td>
                                <td>RP{{ number_format($transaction->total_amount, 2) }}</td>
                                <td>
                                    @if($transaction->payment_status == 'success')
                                    <span class="badge bg-label-success">Completed</span>
                                    @elseif($transaction->payment_status == 'pending')
                                    <span class="badge bg-label-warning">Pending</span>
                                    @else
                                    <span
                                        class="badge bg-label-danger">{{ ucfirst($transaction->payment_status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Customers -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Customers</h5>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="customerOptions" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="customerOptions">
                        <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                        <a class="dropdown-item" href="javascript:void(0);">Export</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <ul class="p-0 m-0">
                    @foreach($recentCustomers as $customer)
                    <li class="d-flex mb-4 pb-1">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-primary">
                                {{ substr($customer->name, 0, 1) }}
                            </span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">{{ $customer->name }}</h6>
                                <small class="text-muted">{{ $customer->email }}</small>
                            </div>
                            <div class="user-progress">
                                <small class="fw-semibold">{{ $customer->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
</div>
<!-- / Content -->
</div>
<!-- Content wrapper -->
@endsection

@push('scripts')
<script>
    // Top Selling Products Chart
    if (document.getElementById('topProductsChart')) {
        const topProductsChartEl = document.getElementById('topProductsChart');
        const products = JSON.parse(topProductsChartEl.dataset.products);
        const quantities = JSON.parse(topProductsChartEl.dataset.quantities);

        const options = {
            series: [{
                name: 'Units Sold',
                data: quantities
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true,
                }
            },
            colors: ['#696cff'],
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val;
                },
                style: {
                    fontSize: '12px',
                    colors: ['#fff']
                }
            },
            xaxis: {
                categories: products,
                labels: {
                    style: {
                        colors: '#8592a3'
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#8592a3'
                    }
                }
            },
            grid: {
                borderColor: 'rgba(0, 0, 0, 0.05)',
                strokeDashArray: 4
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + ' units';
                    }
                }
            }
        };

        const chart = new ApexCharts(topProductsChartEl, options);
        chart.render();
    }

    // Order Status Distribution Chart
if (document.getElementById('orderStatusChart')) {
    const orderStatusChartEl = document.getElementById('orderStatusChart');
    const statuses = JSON.parse(orderStatusChartEl.dataset.statuses);
    const counts = JSON.parse(orderStatusChartEl.dataset.counts);

    // Map statuses to colors
    const statusColors = {
        'pending': '#ffab00',    // warning (yellow)
        'processing': '#03c3ec', // info (blue)
        'shipped': '#696cff',    // primary (purple)
        'delivered': '#71dd37',  // success (green)
        'completed': '#233446',  // dark
        'cancelled': '#ff3e1d'   // danger (red)
    };

    const colors = statuses.map(status => statusColors[status] || '#8592a3');

    const options = {
        series: counts,
        chart: {
            type: 'donut',
            height: 350,
            toolbar: {
                show: false
            }
        },
        labels: statuses.map(status => status.charAt(0).toUpperCase() + status.slice(1)),
        colors: colors,
        legend: {
            position: 'bottom',
            labels: {
                colors: '#8592a3'
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val, opts) {
                return opts.w.config.series[opts.seriesIndex] +
                       ' (' + parseFloat(val).toFixed(1) + '%)';
            },
            style: {
                fontSize: '12px',
                fontWeight: '600'
            },
            dropShadow: {
                enabled: false
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total Orders',
                            formatter: function(w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                            }
                        }
                    }
                }
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    const chart = new ApexCharts(orderStatusChartEl, options);
    chart.render();
}

</script>
@endpush
