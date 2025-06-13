@extends('layouts.app')

@section('title', 'Dasbor Akun Saya')

@section('content')
<section class="wrapper bg-light">
    <div class="container py-14 py-md-16">
        <h1 class="display-4 mb-5 text-center">Dasbor Akun Saya</h1>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row gx-lg-8 gx-xl-12 gy-6">
            {{-- Navigasi Samping atau Tab (Opsional, tergantung desain) --}}
            <div class="col-lg-3">
                <div class="card shadow-lg p-4 rounded-3">
                    <h5 class="mb-3">Navigasi Akun</h5>
                    <ul class="list-unstyled mb-0">
                        <li><a href="{{ route('user.dashboard') }}" class="btn btn-link nav-link active ps-0">Ikhtisar Akun</a></li>
                        <li><a href="{{ route('orders.index') }}" class="btn btn-link nav-link ps-0">Pesanan Saya</a></li>
                        {{-- <li><a href="{{ route('user.profile.edit') }}" class="btn btn-link nav-link ps-0">Edit Profil</a></li> --}}
                        {{-- <li><a href="{{ route('user.addresses') }}" class="btn btn-link nav-link ps-0">Alamat Tersimpan</a></li> --}}
                        {{-- Tambahkan link lain seperti wishlist, review, dll. --}}
                    </ul>
                </div>
            </div>

            {{-- Konten Utama Dasbor --}}
            <div class="col-lg-9">
                <div class="card shadow-lg p-5 rounded-3">
                    <h3 class="h4 mb-4">Selamat Datang, {{ $user->name ?? $user->first_name }}!</h3>
                    <p class="lead">Berikut adalah ringkasan informasi akun Anda.</p>

                    <h4 class="h5 mt-5 mb-3">Informasi Pribadi</h4>
                    <ul class="list-unstyled">
                        <li><strong>Nama Lengkap:</strong> {{ $user->first_name }} {{ $user->last_name }}</li>
                        <li><strong>Email:</strong> {{ $user->email }}</li>
                        <li><strong>Nomor Telepon:</strong> {{ $user->phone ?? 'Belum ada' }}</li>
                    </ul>
                    <a href="#" class="btn btn-sm btn-outline-primary rounded-pill mt-3">Edit Informasi Pribadi</a>

                    <h4 class="h5 mt-5 mb-3">Pesanan Terbaru</h4>
                    @if($user->orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Nomor Pesanan</th>
                                        <th>Tanggal</th>
                                        <th class="text-end">Total</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Ambil 3-5 pesanan terbaru --}}
                                    @foreach($user->orders->take(5) as $order)
                                    <tr>
                                        <td><strong>{{ $order->order_number }}</strong></td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td class="text-end">Rp. {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge {{ $order->order_status == 'processing' ? 'bg-info' : ($order->order_status == 'delivered' ? 'bg-success' : 'bg-secondary') }} text-capitalize">
                                                {{ str_replace('_', ' ', $order->order_status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">Detail</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <a href="{{ route('orders.index') }}" class="btn btn-primary rounded-pill">Lihat Semua Pesanan</a>
                        </div>
                    @else
                        <p>Anda belum memiliki pesanan terbaru.</p>
                        <a href="{{ route('collections') }}" class="btn btn-primary rounded-pill">Mulai Belanja</a>
                    @endif

                    {{-- Anda bisa menambahkan bagian lain seperti alamat tersimpan di sini --}}
                    {{--
                    <h4 class="h5 mt-5 mb-3">Alamat Tersimpan</h4>
                    @if($user->addresses->count() > 0)
                        @foreach($user->addresses as $address)
                            <div class="border rounded p-3 mb-3">
                                <p class="mb-1"><strong>{{ $address->full_name }}</strong></p>
                                <p class="mb-1">{{ $address->address_line1 }}, {{ $address->city }}, {{ $address->province }}, {{ $address->zip_code }}</p>
                                <p class="mb-1">{{ $address->phone_number }}</p>
                                <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill mt-2">Edit</a>
                            </div>
                        @endforeach
                        <a href="{{ route('user.addresses') }}" class="btn btn-primary rounded-pill mt-3">Kelola Alamat</a>
                    @else
                        <p>Anda belum menyimpan alamat.</p>
                        <a href="{{ route('user.addresses') }}" class="btn btn-primary rounded-pill">Tambah Alamat</a>
                    @endif
                    --}}
                </div>
            </div>
        </div>
        <!--/.row -->
    </div>
    <!-- /.container -->
</section>
<!-- /section -->
@endsection
