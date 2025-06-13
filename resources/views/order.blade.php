@extends('layouts.app')

@section('title', 'Daftar Pesanan Saya')

@section('content')
<section class="wrapper bg-light">
    <div class="container py-14 py-md-16">
        <h1 class="display-4 mb-5 text-center">Daftar Pesanan Saya</h1>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row gx-lg-8 gx-xl-12 gy-6">
            <div class="col-12">
                <div class="card shadow-lg p-md-5 p-3">
                    @if($orders->isEmpty())
                        <div class="text-center py-5">
                            <i class="uil uil-box-alt display-1 text-muted mb-3"></i>
                            <p class="lead">Anda belum memiliki pesanan.</p>
                            <a href="{{ route('collections') }}" class="btn btn-primary rounded-pill mt-3">Mulai Belanja</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Nomor Pesanan</th>
                                        <th>Tanggal</th>
                                        <th class="text-end">Total</th>
                                        <th>Status Pembayaran</th>
                                        <th>Status Order</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td><strong>{{ $order->order_number }}</strong></td>
                                        <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                                        <td class="text-end">Rp. {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge {{ $order->payment_status == 'success' ? 'bg-success' : ($order->payment_status == 'pending' || $order->payment_status == 'waiting_payment' ? 'bg-warning' : 'bg-danger') }} text-capitalize">
                                                {{ $order->payment_status }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $order->order_status == 'processing' ? 'bg-info' : ($order->order_status == 'delivered' ? 'bg-success' : 'bg-secondary') }} text-capitalize">
                                                {{ str_replace('_', ' ', $order->order_status) }}
                                            </span>
                                        </td>
                                        <td class="text-center text-nowrap"> {{-- Tambahkan text-nowrap agar tombol tidak pecah baris --}}
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary rounded-pill mb-2 mb-md-0 me-md-2">Lihat Detail</a>
                                            @if($order->payment_status === 'pending' || $order->payment_status === 'waiting_payment' || $order->order_status === 'initiated' || $order->order_status === 'pending_challenge')
                                                {{-- Tombol Lanjutkan Pembayaran juga dibuat btn-sm dan responsive margin --}}
                                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-warning rounded-pill">Lanjutkan Pembayaran</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-center mt-4">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!--/.row -->
    </div>
    <!-- /.container -->
</section>
<!-- /section -->
@endsection
