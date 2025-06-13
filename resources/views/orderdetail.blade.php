@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
<section class="wrapper bg-light">
    <div class="container py-14 py-md-16">
        {{-- Pengecekan awal untuk variabel $order --}}
        @if($order)
            <h1 class="display-4 mb-5 text-center">Detail Pesanan #{{ $order->order_number }}</h1>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row gx-lg-8 gx-xl-12 gy-6">
                {{-- Bagian Status Pesanan (Proses Pelacakan) --}}
                <div class="col-12 mb-8">
                    <div class="card shadow-lg p-5">
                        <h3 class="h4 mb-4 text-center">Status Pesanan Anda</h3>
                        {{-- Menggunakan justify-content-center dan col-md-2 untuk 5 kolom dalam satu baris --}}
                        <div class="row process-wrapper line text-center justify-content-center">
                            {{-- Contoh Status: Sesuaikan dengan status actual di order_status Anda --}}
                            <div class="col-md-2 col-6"> {{-- col-6 agar tetap responsif di mobile (2 kolom per baris) --}}
                                <span class="icon btn btn-circle btn-lg mb-4
                                    {{ $order->order_status == 'initiated' ? 'btn-primary' : 'btn-soft-primary' }} pe-none">
                                    <i class="uil uil-file-alt"></i>
                                </span>
                                <h4 class="mb-1">Pesanan Dibuat</h4>
                            </div>
                            <div class="col-md-2 col-6">
                                <span class="icon btn btn-circle btn-lg mb-4
                                    {{ $order->order_status == 'waiting_payment' || $order->order_status == 'pending_challenge' ? 'btn-primary' : 'btn-soft-primary' }} pe-none">
                                    <i class="uil uil-hourglass"></i>
                                </span>
                                <h4 class="mb-1">Menunggu Pembayaran</h4>
                            </div>
                            <div class="col-md-2 col-6 mt-md-0 mt-4"> {{-- Hapus mt-4 pada col-md-X ini jika ingin selalu sebaris --}}
                                <span class="icon btn btn-circle btn-lg mb-4
                                    {{ $order->order_status == 'processing' ? 'btn-primary' : 'btn-soft-primary' }} pe-none">
                                    <i class="uil uil-box"></i>
                                </span>
                                <h4 class="mb-1">Diproses & Dikemas</h4>
                            </div>
                            <div class="col-md-2 col-6 mt-md-0 mt-4"> {{-- Hapus mt-4 pada col-md-X ini jika ingin selalu sebaris --}}
                                <span class="icon btn btn-circle btn-lg mb-4
                                    {{ $order->order_status == 'shipped' ? 'btn-primary' : 'btn-soft-primary' }} pe-none">
                                    <i class="uil uil-truck"></i>
                                </span>
                                <h4 class="mb-1">Sedang Dikirim</h4>
                            </div>
                            <div class="col-md-2 col-6 mt-md-0 mt-4"> {{-- Ubah mt-4 menjadi mt-md-0 agar tidak ada margin di atas pada layar md ke atas --}}
                                <span class="icon btn btn-circle btn-lg mb-4
                                    {{ $order->order_status == 'delivered' ? 'btn-success' : 'btn-soft-primary' }} pe-none">
                                    <i class="uil uil-check-circle"></i>
                                </span>
                                <h4 class="mb-1">Telah Diterima</h4>
                            </div>
                            {{-- Tambahkan status lain sesuai kebutuhan e.g., cancelled, refunded --}}
                        </div>
                    </div>
                </div>

                {{-- Informasi Pesanan dan Pelacakan --}}
                <div class="col-lg-7">
                    <div class="card shadow-lg p-5 mb-5">
                        <h3 class="h4 mb-4">Informasi Pesanan</h3>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Nomor Pesanan:</strong><br> {{ $order->order_number }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Tanggal Pesanan:</strong><br> {{ $order->created_at->format('d M Y, H:i') }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Status Pembayaran:</strong><br> <span class="badge {{ $order->payment_status == 'success' ? 'bg-success' : ($order->payment_status == 'pending' ? 'bg-warning' : 'bg-danger') }} text-capitalize">{{ $order->payment_status }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Status Order:</strong><br> <span class="badge {{ $order->order_status == 'processing' ? 'bg-info' : ($order->order_status == 'delivered' ? 'bg-success' : 'bg-secondary') }} text-capitalize">{{ str_replace('_', ' ', $order->order_status) }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Metode Pembayaran:</strong><br> {{ $order->payment_method }}
                            </div>
                            @if($order->midtrans_transaction_id)
                            <div class="col-md-6 mb-3">
                                <strong>ID Transaksi Midtrans:</strong><br> {{ $order->midtrans_transaction_id }}
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Detail Produk --}}
                    <div class="card shadow-lg p-5">
                        <h3 class="h4 mb-4">Produk dalam Pesanan</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-center">Jumlah</th>
                                        <th class="text-end">Harga Satuan</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($order->orderItems as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->product_name }}</strong>
                                            @if($item->selected_size || $item->selected_color)
                                                <small class="d-block text-muted">
                                                    @if($item->selected_size) Ukuran: {{ $item->selected_size }} @endif
                                                    @if($item->selected_color) Warna: {{ $item->selected_color }} @endif
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">Rp. {{ number_format($item->price_per_item, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp. {{ number_format($item->total_price, 0, ',', '.') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada produk dalam pesanan ini.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Subtotal Produk:</th>
                                        <th class="text-end">Rp. {{ number_format($order->subtotal_amount, 0, ',', '.') }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-end">Biaya Pengiriman:</th>
                                        <th class="text-end">Rp. {{ number_format($order->shipping_cost, 0, ',', '.') }} ({{ $order->shipping_method }})</th>
                                    </tr>
                                    @if($order->discount_amount > 0)
                                    <tr>
                                        <th colspan="3" class="text-end">Diskon:</th>
                                        <th class="text-end">- Rp. {{ number_format($order->discount_amount, 0, ',', '.') }}</th>
                                    </tr>
                                    @endif
                                    <tr class="table-dark">
                                        <th colspan="3" class="text-end">Total Akhir:</th>
                                        <th class="text-end">Rp. {{ number_format($order->total_amount, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Informasi Alamat --}}
                <div class="col-lg-5">
                    <div class="card shadow-lg p-5 mb-5">
                        <h3 class="h4 mb-4">Alamat Penagihan</h3>
                        <p class="mb-1">{{ $order->billing_first_name }} {{ $order->billing_last_name }}</p>
                        <p class="mb-1">{{ $order->billing_email }}</p>
                        <p class="mb-1">{{ $order->billing_phone }}</p>
                        <p class="mb-1">{{ $order->billing_address_line_1 }}</p>
                        @if($order->billing_address_line_2)
                        <p class="mb-1">{{ $order->billing_address_line_2 }}</p>
                        @endif
                        <p class="mb-1">
                            {{-- Perlu lookup nama kota/provinsi jika hanya ID yang disimpan --}}
                            {{-- Jika Anda menyimpan nama string, gunakan $order->billing_city, $order->billing_province --}}
                            {{-- Contoh: {{ $order->billingCity->name ?? 'N/A' }}, {{ $order->billingProvince->name ?? 'N/A' }} --}}
                            {{ $order->billing_zip_code }}
                        </p>
                    </div>

                    <div class="card shadow-lg p-5">
                        <h3 class="h4 mb-4">Alamat Pengiriman</h3>
                        <p class="mb-1">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</p>
                        <p class="mb-1">{{ $order->shipping_email }}</p>
                        <p class="mb-1">{{ $order->shipping_phone_number }}</p>
                        <p class="mb-1">{{ $order->shipping_address_line_1 }}</p>
                        @if($order->shipping_address_line_2)
                        <p class="mb-1">{{ $order->shipping_address_line_2 }}</p>
                        @endif
                        <p class="mb-1">
                            {{-- Perlu lookup nama kota/provinsi jika hanya ID yang disimpan --}}
                            {{-- Contoh: {{ $order->shippingCity->name ?? 'N/A' }}, {{ $order->shippingProvince->name ?? 'N/A' }} --}}
                            {{ $order->shipping_zip_code }}
                        </p>
                    </div>
                </div>
            </div>
            <!--/.row -->

            <div class="text-center mt-8">
                <a href="{{ route('orders.index') }}" class="btn btn-primary rounded-pill me-2">
                    <i class="uil uil-arrow-left me-1"></i> Kembali ke Daftar Pesanan
                </a>
                {{-- Tombol untuk aksi tambahan, misal cetak faktur, atau batalkan pesanan (jika status memungkinkan) --}}
                @if($order->payment_status === 'pending' || $order->payment_status === 'waiting_payment' || $order->order_status === 'initiated' || $order->order_status === 'pending_challenge')
                    <button class="btn btn-warning rounded-pill" onclick="snap.pay('{{ $order->midtrans_snap_token }}')">
                        Lanjutkan Pembayaran
                    </button>
                @endif
            </div>

        @else
            {{-- Tampilan jika order tidak ditemukan atau variabel $order null --}}
            <div class="col-12 text-center">
                <div class="card shadow-lg p-5">
                    <h3 class="h4 mb-4 text-danger">Pesanan Tidak Ditemukan</h3>
                    <p class="lead">Maaf, kami tidak dapat menemukan detail pesanan yang Anda cari.</p>
                    <p>Mohon periksa kembali tautan yang Anda gunakan atau kembali ke daftar pesanan Anda.</p>
                    <div class="mt-4">
                        <a href="{{ route('orders.index') }}" class="btn btn-primary rounded-pill">
                            <i class="uil uil-list-ul me-1"></i> Lihat Daftar Pesanan
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-primary rounded-pill ms-2">
                            <i class="uil uil-home me-1"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        @endif

    </div>
    <!-- /.container -->
</section>
<!-- /section -->
@endsection
