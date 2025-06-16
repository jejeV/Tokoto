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
            <img src="{{ asset('assets/admin/img/unicons/chart-success.png') }}" alt="Credit Card" class="rounded" />
          </div>
        </div>
        <span>Total Pendapatan</span>
        <h3 class="card-title text-nowrap mb-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
        @if($growthPercentage !== null)
            <small class="text-success fw-semibold">
                <i class="bx {{ $growthPercentage >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' }}"></i>
                {{ number_format(abs($growthPercentage), 2) }}%
            </small>
        @endif
      </div>
    </div>
  </div>

  <!-- Total Pesanan -->
  <div class="col-lg-3 col-md-6 col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="card-title d-flex align-items-start justify-content-between">
          <div class="avatar flex-shrink-0">
            <img src="{{ asset('assets/admin/img/unicons/chart.png') }}" alt="Credit Card" class="rounded" />
          </div>
        </div>
        <span>Total Pesanan</span>
        {{-- Menggunakan data dari controller --}}
        <h3 class="card-title text-nowrap mb-1">{{ number_format($totalOrders, 0, ',', '.') }}</h3>
        {{-- Placeholder untuk growth, tidak ada di controller --}}
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
            <img src="{{ asset('assets/admin/img/unicons/cc-primary.png') }}" alt="Credit Card" class="rounded" />
          </div>
        </div>
        <span>Total Produk</span>
        {{-- Menggunakan data dari controller --}}
        <h3 class="card-title text-nowrap mb-1">{{ number_format($totalProducts, 0, ',', '.') }}</h3>
        {{-- Placeholder untuk growth, tidak ada di controller --}}
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
            <img src="{{ asset('assets/admin/img/unicons/cc-primary.png') }}" alt="Credit Card" class="rounded" />
          </div>
        </div>
        <span>Total Pelanggan</span>
        <h3 class="card-title text-nowrap mb-1">{{ number_format($totalCustomers, 0, ',', '.') }}</h3>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Total Revenue Chart -->
  <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-2 mb-4">
    <div class="card">
      <div class="row row-bordered g-0">
        <div class="col-md-8">
          <h5 class="card-header m-0 me-2 pb-3">Total Revenue</h5>
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
                  {{ $currentYear }}
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="growthReportId">
                  <a class="dropdown-item" href="javascript:void(0);" data-year="{{ $previousYear }}">{{ $previousYear }}</a>
                </div>
              </div>
            </div>
          </div>
          <div id="growthChart"></div>
          <div class="text-center fw-semibold pt-3 mb-2">{{ number_format(abs($growthPercentage), 2) }}% Company Growth</div>
          <div class="d-flex px-xxl-4 px-lg-2 p-4 gap-xxl-3 gap-lg-1 gap-3 justify-content-between">
            <div class="d-flex">
              <div class="me-2">
                <span class="badge bg-label-primary p-2"><i class="bx bx-dollar text-primary"></i></span>
              </div>
              <div class="d-flex flex-column">
                <small>{{ $currentYear }}</small>
                <h6 class="mb-0">Rp {{ number_format($revenueCurrentYear, 0, ',', '.') }}</h6>
              </div>
            </div>
            <div class="d-flex">
              <div class="me-2">
                <span class="badge bg-label-info p-2"><i class="bx bx-wallet text-info"></i></span>
              </div>
              <div class="d-flex flex-column">
                <small>{{ $previousYear }}</small>
                <h6 class="mb-0">Rp {{ number_format($revenuePreviousYear, 0, ',', '.') }}</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Total Revenue -->

  <!-- Pesanan Terbaru (Transactions) -->
  <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
    <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Transactions</h5>
          <div class="dropdown">
            <button
              class="btn p-0"
              type="button"
              id="transactionID"
              data-bs-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <i class="bx bx-dots-vertical-rounded"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
              <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <ul class="p-0 m-0">
            @forelse($latestTransactions as $transaction)
                <li class="d-flex mb-4 pb-1">
                  <div class="avatar flex-shrink-0 me-3">
                    @php
                        $iconSrc = '';
                        switch ($transaction->midtrans_payment_type) {
                            case 'bank_transfer':
                            case 'permata':
                            case 'bca_va':
                            case 'bni_va':
                            case 'bri_va':
                            case 'mandiri_va':
                                $iconSrc = asset('assets/admin/img/unicons/wallet.png'); // Icon wallet/bank
                                break;
                            case 'credit_card':
                                $iconSrc = asset('assets/admin/img/unicons/cc-success.png'); // Icon credit card
                                break;
                            case 'gopay':
                                $iconSrc = asset('assets/admin/img/unicons/paypal.png'); // Contoh: gunakan icon PayPal jika tidak ada icon GoPay
                                break;
                            default:
                                $iconSrc = asset('assets/admin/img/unicons/chart.png'); // Default icon
                                break;
                        }
                    @endphp
                    <img src="{{ $iconSrc }}" alt="Payment Type" class="rounded" />
                  </div>
                  <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                    <div class="me-2">
                      <small class="text-muted d-block mb-1">{{ ucfirst(str_replace('_', ' ', $transaction->midtrans_payment_type ?? 'Unknown')) }}</small>
                      <h6 class="mb-0">Order #{{ $transaction->order_number }}</h6>
                    </div>
                    <div class="user-progress d-flex align-items-center gap-1">
                      {{-- Tampilkan gross_amount jika ada, atau total_amount --}}
                      <h6 class="mb-0">Rp {{ number_format($transaction->midtrans_gross_amount ?? $transaction->total_amount, 0, ',', '.') }}</h6>
                    </div>
                  </div>
                </li>
            @empty
                <li class="d-flex mb-4 pb-1">
                    <div class="text-center w-100 py-3">Tidak ada transaksi terbaru.</div>
                </li>
            @endforelse
          </ul>
        </div>
      </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const totalRevenue = {{ $totalRevenue }};
  const revenueCurrentYear = {{ $revenueCurrentYear }};
  const revenuePreviousYear = {{ $revenuePreviousYear }};
  const growthPercentage = {{ $growthPercentage }};
  const currentYear = {{ $currentYear }};
  const previousYear = {{ $previousYear }};

  // Asumsi 'config.colors' tersedia secara global dari layout atau skrip lain.
  // Jika tidak, Anda perlu mendefinisikannya di sini, contoh:
  // const config = {
  //     colors: {
  //         primary: '#696cff',
  //         info: '#03c3ec',
  //         danger: '#ff3e1d',
  //         warning: '#ffab00',
  //         success: '#71dd37',
  //         secondary: '#8592a3',
  //         dark: '#233446',
  //         'gray-100': '#f8f9fa', // Contoh
  //         axisColor: '#8592a3', // Contoh
  //         white: '#ffffff',
  //         borderColor: '#e2e2e2' // Contoh
  //     }
  // };

  // Generate data bulanan dummy untuk chart Total Revenue
  // Ini adalah simulasi karena controller hanya memberikan total tahunan.
  // Dalam aplikasi nyata, Anda akan mendapatkan data ini dari backend.
  const monthlyRevenueDataCurrentYear = Array.from({length: 12}, (_, i) => Math.floor(revenueCurrentYear / 12 + Math.random() * 500000)); // Contoh data dummy
  const monthlyRevenueDataPreviousYear = Array.from({length: 12}, (_, i) => Math.floor(revenuePreviousYear / 12 + Math.random() * 500000)); // Contoh data dummy

  // Inisialisasi chart Total Revenue
  let revenueChartEl = document.getElementById('totalRevenueChart');
  let revenueChartOptions = {
    series: [
      {
        name: 'Pendapatan ' + currentYear,
        data: monthlyRevenueDataCurrentYear // Data bulanan dummy
      },
      {
        name: 'Pendapatan ' + previousYear,
        data: monthlyRevenueDataPreviousYear // Data bulanan dummy
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
    colors: [config.colors.primary, config.colors.info], // Menggunakan warna dari config
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
      borderColor: config.colors.borderColor, // Menggunakan warna dari config
      padding: {
        top: 0,
        bottom: -8,
        left: 20,
        right: 20
      }
    },
    xaxis: {
      categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'], // Kategori bulan
      labels: {
        style: {
          fontSize: '13px',
          colors: config.colors.axisColor // Menggunakan warna dari config
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
          return 'Rp ' + (val / 1000000).toFixed(0) + 'jt'; // Format menjadi jutaan
        }
      }
    },
    responsive: [
      {
        breakpoint: 1700,
        options: { plotOptions: { bar: { borderRadius: 10, columnWidth: '32%' } } }
      },
      {
        breakpoint: 1580,
        options: { plotOptions: { bar: { borderRadius: 10, columnWidth: '35%' } } }
      },
      {
        breakpoint: 1440,
        options: { plotOptions: { bar: { borderRadius: 10, columnWidth: '42%' } } }
      },
      {
        breakpoint: 1300,
        options: { plotOptions: { bar: { borderRadius: 10, columnWidth: '48%' } } }
      },
      {
        breakpoint: 1200,
        options: { plotOptions: { bar: { borderRadius: 10, columnWidth: '40%' } } }
      },
      {
        breakpoint: 1040,
        options: { plotOptions: { bar: { borderRadius: 11, columnWidth: '48%' } } }
      },
      {
        breakpoint: 991,
        options: { plotOptions: { bar: { borderRadius: 10, columnWidth: '30%' } } }
      },
      {
        breakpoint: 840,
        options: { plotOptions: { bar: { borderRadius: 10, columnWidth: '35%' } } }
      },
      {
        breakpoint: 768,
        options: { plotOptions: { bar: { borderRadius: 10, columnWidth: '28%' } } }
      },
      {
        breakpoint: 640,
        options: { plotOptions: { bar: { borderRadius: 10, columnWidth: '32%' } } }
      },
      {
        breakpoint: 576,
        options: { plotOptions: { bar: { borderRadius: 10, columnWidth: '37%' } } }
      },
      {
        breakpoint: 480,
        options: { plotOptions: { bar: { borderRadius: 10, columnWidth: '45%' } } }
      },
      {
        breakpoint: 420,
        options: { plotOptions: { bar: { borderRadius: 10, columnWidth: '52%' } } }
      },
      {
        breakpoint: 380,
        options: { plotOptions: { bar: { borderRadius: 10, columnWidth: '60%' } } }
      }
    ],
    states: {
      hover: { filter: { type: 'none' } },
      active: { filter: { type: 'none' } }
    }
  };

  if (typeof revenueChartEl !== undefined && revenueChartEl !== null) {
    const revenueChart = new ApexCharts(revenueChartEl, revenueChartOptions);
    revenueChart.render();
  }

  // Inisialisasi chart pertumbuhan (radial bar chart)
  let growthChartEl = document.getElementById('growthChart');
  let growthChartOptions = {
    series: [Math.round(growthPercentage)], // Menggunakan persentase pertumbuhan dari controller
    chart: {
      height: 240,
      type: 'radialBar',
      sparkline: { enabled: true }
    },
    plotOptions: {
      radialBar: {
        offsetY: -20,
        startAngle: -90,
        endAngle: 90,
        hollow: {
          size: '60%',
          margin: 0,
          background: 'transparent',
          image: undefined,
          imageOffsetX: 0,
          imageOffsetY: 0,
          position: 'front',
          dropShadow: {
            enabled: false,
            top: 3,
            left: 0,
            blur: 4,
            opacity: 0.24
          }
        },
        dataLabels: {
          name: {
            show: false
          },
          value: {
            offsetY: -2,
            fontSize: '22px',
            color: config.colors.headingColor,
            formatter: function (val) {
              return val + '%';
            }
          }
        }
      }
    },
    fill: {
      type: 'gradient',
      gradient: {
        shade: 'dark',
        shadeIntensity: 0.15,
        inverseColors: false,
        opacityFrom: 1,
        opacityTo: 1,
        stops: [0, 50, 65, 90]
      }
    },
    stroke: {
      dashArray: 5
    },
    grid: {
      padding: {
        top: -10,
        bottom: -10,
        left: -10,
        right: -10
      }
    },
    responsive: [
      {
        breakpoint: 1700,
        options: {
          chart: {
            height: 230
          }
        }
      },
      {
        breakpoint: 1440,
        options: {
          chart: {
            height: 210
          }
        }
      },
      {
        breakpoint: 1300,
        options: {
          chart: {
            height: 200
          }
        }
      },
      {
        breakpoint: 1200,
        options: {
          chart: {
            height: 180
          }
        }
      },
      {
        breakpoint: 1040,
        options: {
          chart: {
            height: 170
          }
        }
      },
      {
        breakpoint: 991,
        options: {
          chart: {
            height: 160
          }
        }
      },
      {
        breakpoint: 840,
        options: {
          chart: {
            height: 150
          }
        }
      },
      {
        breakpoint: 768,
        options: {
          chart: {
            height: 140
          }
        }
      }
    ],
    colors: [config.colors.primary]
  };

  if (typeof growthChartEl !== undefined && growthChartEl !== null) {
    const growthChart = new ApexCharts(growthChartEl, growthChartOptions);
    growthChart.render();
  }

</script>
@endpush
