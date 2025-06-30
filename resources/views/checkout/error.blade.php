@extends('layouts.app')

@section('title', 'Pembayaran Gagal')

@section('content')
<section class="wrapper bg-light">
    <div class="container pt-12 pt-md-14 pb-14 pb-md-16">
        <div class="row gx-md-8 gx-xl-12 gy-12 d-flex justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow-lg text-center p-md-8 p-5">
                    <div class="card-body">
                        {{-- Icon Gagal/Error (Cross Circle) --}}
                        <div class="mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#dc3545" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                            </svg>
                        </div>

                        <h1 class="display-5 text-danger mb-3">Pembayaran Gagal!</h1>
                        <p class="lead">Terjadi masalah saat memproses pembayaran Anda.</p>

                        @if(request()->query('message'))
                            <div class="alert alert-warning mt-4 mx-auto" style="max-width: 400px;" role="alert">
                                <strong>Pesan Error:</strong> {{ urldecode(request()->query('message')) }}
                            </div>
                        @endif

                        @if($order)
                            <div class="text-start mx-auto mt-4" style="max-width: 400px;">
                                <h4 class="h5 mb-3">Detail Pesanan Anda:</h4>
                                <ul class="list-unstyled">
                                    <li class="d-flex justify-content-between">
                                        <strong>Nomor Pesanan:</strong>
                                        <span>{{ $order->order_number }}</span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <strong>Total Pembayaran:</strong>
                                        <span>Rp. {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <strong>Status Pembayaran:</strong>
                                        <span class="text-capitalize text-danger">{{ $order->payment_status }}</span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <strong>Metode Pembayaran:</strong>
                                        <span class="text-capitalize">{{ $order->payment_method }}</span>
                                    </li>
                                    @if($order->midtrans_transaction_id)
                                        <li class="d-flex justify-content-between">
                                            <strong>ID Transaksi Midtrans:</strong>
                                            <span>{{ $order->midtrans_transaction_id }}</span>
                                        </li>
                                    @endif
                                    @if($order->midtrans_status_code)
                                        <li class="d-flex justify-content-between">
                                            <strong>Kode Status:</strong>
                                            <span>{{ $order->midtrans_status_code }}</span>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            <p class="mt-4">
                                Mohon coba lagi atau hubungi dukungan pelanggan jika masalah berlanjut.
                            </p>
                        @else
                            <p class="mt-4">
                                Detail pesanan tidak dapat ditemukan. Mohon periksa kembali pesanan Anda atau hubungi dukungan pelanggan.
                            </p>
                        @endif

                        <div class="mt-5">
                            <a href="{{ route('home') }}" class="btn btn-primary rounded-pill me-2">
                                Kembali ke Beranda
                            </a>
                            @if($order)
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary rounded-pill">
                                    Lihat Detail Pesanan
                                </a>
                            @else
                                <a href="{{ route('orders.index') }}" class="btn btn-outline-primary rounded-pill">
                                    Lihat Daftar Pesanan
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

