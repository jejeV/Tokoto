@extends('layouts.app')

@section('title', 'Pembayaran Berhasil')

@section('content')
<section class="wrapper bg-light">
    <div class="container pt-12 pt-md-14 pb-14 pb-md-16">
        <div class="row gx-md-8 gx-xl-12 gy-12 d-flex justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow-lg text-center p-md-8 p-5">
                    <div class="card-body">
                        {{-- Icon Sukses --}}
                        <div class="mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#28a745" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </svg>
                        </div>

                        <h1 class="display-5 text-success mb-3">Pembayaran Berhasil!</h1>
                        <p class="lead">Terima kasih atas pesanan Anda. Kami telah menerima pembayaran Anda.</p>

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
                                        <span class="text-capitalize">{{ $order->payment_status }}</span>
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
                                </ul>
                            </div>
                            <p class="mt-4">
                                Anda akan menerima email konfirmasi pesanan dalam beberapa saat.
                            </p>
                        @else
                            <p class="mt-4">
                                Detail pesanan tidak dapat ditemukan. Mohon periksa email Anda untuk informasi lebih lanjut.
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
