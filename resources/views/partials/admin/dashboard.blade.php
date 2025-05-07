@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row">
  <!-- Total Pendapatan -->
  <div class="col-lg-3 col-md-6 col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="card-title d-flex align-items-start justify-content-between">
          <div class="avatar flex-shrink-0">
            <img src="{{ asset('assets/admin/img/icons/unicons/wallet-info.png') }}" alt="Credit Card" class="rounded" />
          </div>
        </div>
        <span>Total Pendapatan</span>
        {{-- <h3 class="card-title text-nowrap mb-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3> --}}
        {{-- <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +{{ $revenueGrowth }}%</small> --}}
      </div>
    </div>
  </div>

  <!-- Total Pesanan -->
  <div class="col-lg-3 col-md-6 col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="card-title d-flex align-items-start justify-content-between">
          <div class="avatar flex-shrink-0">
            <img src="{{ asset('assets/admin/img/icons/unicons/cart.png') }}" alt="Credit Card" class="rounded" />
          </div>
        </div>
        <span>Total Pesanan</span>
        {{-- <h3 class="card-title text-nowrap mb-1">{{ $totalOrders }}</h3> --}}
        {{-- <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +{{ $orderGrowth }}%</small> --}}
      </div>
    </div>
  </div>

  <!-- Total Produk -->
  <div class="col-lg-3 col-md-6 col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="card-title d-flex align-items-start justify-content-between">
          <div class="avatar flex-shrink-0">
            <img src="{{ asset('assets/admin/img/icons/unicons/box.png') }}" alt="Credit Card" class="rounded" />
          </div>
        </div>
        <span>Total Produk</span>
        {{-- <h3 class="card-title text-nowrap mb-1">{{ $totalProducts }}</h3> --}}
        {{-- <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +{{ $productGrowth }}%</small> --}}
      </div>
    </div>
  </div>

  <!-- Total Pelanggan -->
  <div class="col-lg-3 col-md-6 col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="card-title d-flex align-items-start justify-content-between">
          <div class="avatar flex-shrink-0">
            <img src="{{ asset('assets/admin/img/icons/unicons/user.png') }}" alt="Credit Card" class="rounded" />
          </div>
        </div>
        <span>Total Pelanggan</span>
        {{-- <h3 class="card-title text-nowrap mb-1">{{ $totalCustomers }}</h3> --}}
        {{-- <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +{{ $customerGrowth }}%</small> --}}
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Grafik Pendapatan -->
  <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-2 mb-4">
    <div class="card">
      <div class="row row-bordered g-0">
        <div class="col-md-8">
          <h5 class="card-header m-0 me-2 pb-3">Pendapatan Bulanan</h5>
          <div id="totalRevenueChart" class="px-2"></div>
        </div>
        <div class="col-md-4">
          <div class="card-body">
            <div class="text-center">
              <div class="dropdown">
                <button
                  class="btn btn-sm btn-outline-primary dropdown-toggle"
                  type="button"
                  id="growthReportId"
                  data-bs-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false"
                >
                  {{ now()->year }}
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="growthReportId">
                  @for($i = now()->year - 1; $i >= now()->year - 3; $i--)
                    <a class="dropdown-item" href="javascript:void(0);">{{ $i }}</a>
                  @endfor
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Pesanan Terbaru -->
  <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Pesanan Terbaru</h5>
        <a href="..." class="btn btn-sm btn-outline-primary">Lihat Semua</a>
      </div>
      <div class="card-body">
        <ul class="p-0 m-0">
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <span class="avatar-initial rounded bg-label-primary">
                <i class="bx bx-receipt"></i>
              </span>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                {{-- <small class="text-muted d-block mb-1">#{{ $order->order_number }}</small> --}}
                {{-- <h6 class="mb-0">{{ $order->customer_name }}</h6> --}}
              </div>
              <div class="user-progress d-flex align-items-center gap-1">
                {{-- <h6 class="mb-0">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h6> --}}
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Inisialisasi chart pendapatan
  let revenueChartEl = document.getElementById('totalRevenueChart'),
    revenueChartOptions = {
      series: [
        {
          name: 'Pendapatan',
          data: '300'
        }
      ],
      chart: {
        height: 300,
        stacked: true,
        type: 'bar',
        toolbar: { show: false }
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '33%',
          borderRadius: 12,
          startingShape: 'rounded',
          endingShape: 'rounded'
        }
      },
      colors: ['#696cff'],
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'smooth',
        width: 6,
        lineCap: 'round',
        colors: [config.colors.white]
      },
      legend: {
        show: true,
        horizontalAlign: 'left',
        position: 'top',
        markers: {
          height: 8,
          width: 8,
          radius: 12,
          offsetX: -3
        },
        itemMargin: {
          horizontal: 10
        }
      },
      grid: {
        borderColor: config.colors.border,
        padding: {
          top: 0,
          bottom: -8,
          left: 20,
          right: 20
        }
      },
      xaxis: {
        categories: "",
        labels: {
          style: {
            fontSize: '13px',
            colors: config.colors.axisColor
          }
        },
        axisTicks: {
          show: false
        },
        axisBorder: {
          show: false
        }
      },
      yaxis: {
        labels: {
          formatter: function(val) {
            return 'Rp ' + (val / 1000).toFixed(0) + 'k';
          }
        }
      },
      responsive: [
        {
          breakpoint: 1700,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '32%'
              }
            }
          }
        },
        {
          breakpoint: 1580,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '35%'
              }
            }
          }
        },
        {
          breakpoint: 1440,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '42%'
              }
            }
          }
        },
        {
          breakpoint: 1300,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '48%'
              }
            }
          }
        },
        {
          breakpoint: 1200,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '40%'
              }
            }
          }
        },
        {
          breakpoint: 1040,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 11,
                columnWidth: '48%'
              }
            }
          }
        },
        {
          breakpoint: 991,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '30%'
              }
            }
          }
        },
        {
          breakpoint: 840,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '35%'
              }
            }
          }
        },
        {
          breakpoint: 768,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '28%'
              }
            }
          }
        },
        {
          breakpoint: 640,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '32%'
              }
            }
          }
        },
        {
          breakpoint: 576,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '37%'
              }
            }
          }
        },
        {
          breakpoint: 480,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '45%'
              }
            }
          }
        },
        {
          breakpoint: 420,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '52%'
              }
            }
          }
        },
        {
          breakpoint: 380,
          options: {
            plotOptions: {
              bar: {
                borderRadius: 10,
                columnWidth: '60%'
              }
            }
          }
        }
      ],
      states: {
        hover: {
          filter: {
            type: 'none'
          }
        },
        active: {
          filter: {
            type: 'none'
          }
        }
      }
    };

  if (typeof revenueChartEl !== undefined && revenueChartEl !== null) {
    const revenueChart = new ApexCharts(revenueChartEl, revenueChartOptions);
    revenueChart.render();
  }
</script>
@endpush
