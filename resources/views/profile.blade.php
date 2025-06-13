@extends('layouts.app')

@section('title', 'Dasbor Akun Saya')

@section('content')
<section class="wrapper bg-gray">
    <div class="container py-3 py-md-5">
        <nav class="d-inline-block" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Account Information</li>
            </ol>
        </nav>
    </div>
</section>

<section class="wrapper bg-light">
    <div class="container py-14 py-md-16">
        <h1 class="display-4 mb-5 text-center">Account Information</h1>

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

        {{-- Menampilkan Error Validasi (untuk form yang sedang aktif) --}}
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Ada masalah dengan input Anda:
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row gx-lg-8 gx-xl-12 gy-6">
            {{-- Navigasi Samping (Tabs Nav) --}}
            <div class="col-lg-3">
                <div class="card shadow-lg p-4 rounded-3">
                    <h5 class="mb-3">Account</h5>
                    <ul class="nav nav-tabs nav-tabs-vertical nav-tabs-vertical-left flex-column" id="accountTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="overview-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Account Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Edit Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="password-tab" data-bs-toggle="tab" href="#password" role="tab" aria-controls="password" aria-selected="false">Change Password</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="orders-tab" data-bs-toggle="tab" href="#orders" role="tab" aria-controls="orders" aria-selected="false">My Order</a>
                        </li>
                        {{-- Tambahkan link navigasi lain di sini jika ada (misal: Alamat Tersimpan, Wishlist) --}}
                    </ul>
                </div>
            </div>

            {{-- Konten Utama Dashboard (Tab Content) --}}
            <div class="col-lg-9">
                <div class="tab-content" id="accountTabsContent">

                    {{-- Tab Ikhtisar Akun --}}
                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                        <div class="card shadow-lg p-5 rounded-3">
                            <h3 class="h4 mb-4">Selamat Datang, {{ $user->name ?? ($user->first_name . ' ' . $user->last_name) }}!</h3>
                            <h4 class="h5 mt-5 mb-3">Informasi Pribadi</h4>
                            <ul class="list-unstyled">
                                <li><strong>Nama Lengkap:</strong> {{ $user->name }}</li>
                                <li><strong>Email:</strong> {{ $user->email }}</li>
                                <li><strong>Nomor Telepon:</strong> {{ $user->phone_number ?? 'Belum ada' }}</li>
                                <li><strong>Alamat:</strong> {{ $user->address ?? 'Belum ada' }}</li>
                            </ul>
                            <button class="btn btn-sm btn-outline-primary rounded-pill mt-3" onclick="document.getElementById('profile-tab').click()">Edit Informasi Pribadi</button>

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
                        </div>
                    </div>

                    {{-- Tab Edit Profil --}}
                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="card shadow-lg p-5 rounded-3">
                            <h3 class="h4 mb-4">Edit Informasi Pribadi</h3>
                            <form action="{{ route('profile.update.info') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                           value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                           value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">Nomor Telepon</label>
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number"
                                           value="{{ old('phone_number', $user->phone_number) }}">
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Alamat</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Jika Anda menggunakan first_name dan last_name di model User, Anda bisa tambahkan ini: --}}
                                {{--
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">Nama Depan</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name"
                                           value="{{ old('first_name', $user->first_name) }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Nama Belakang</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name"
                                           value="{{ old('last_name', $user->last_name) }}">
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                --}}

                                <button type="submit" class="btn btn-primary rounded-pill">Simpan Perubahan</button>
                            </form>
                        </div>
                    </div>

                    {{-- Tab Ubah Password --}}
                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                        <div class="card shadow-lg p-5 rounded-3">
                            <h3 class="h4 mb-4">Ubah Password</h3>
                            <form action="{{ route('profile.update.password') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Password Saat Ini</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Password Baru</label>
                                    <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required>
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                    <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" id="new_password_confirmation" name="new_password_confirmation" required>
                                    @error('new_password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary rounded-pill">Ubah Password</button>
                            </form>
                        </div>
                    </div>

                    {{-- Tab Pesanan Saya --}}
                    <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                        <div class="card shadow-lg p-5 rounded-3">
                            <h3 class="h4 mb-4">Pesanan Saya</h3>
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
                                            @foreach($user->orders as $order) {{-- Menampilkan semua pesanan, bukan hanya take(5) --}}
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
                                    {{-- Jika ada pagination, Anda bisa tambahkan link pagination di sini --}}
                                    {{-- {{ $user->orders->links() }} --}}
                                    <a href="{{ route('orders.index') }}" class="btn btn-primary rounded-pill">Lihat Semua Pesanan</a>
                                </div>
                            @else
                                <p>Anda belum memiliki pesanan.</p>
                                <a href="{{ route('collections') }}" class="btn btn-primary rounded-pill">Mulai Belanja</a>
                            @endif
                        </div>
                    </div>

                    {{-- Anda bisa menambahkan Tab lain di sini (misal: Alamat Tersimpan, Wishlist) --}}
                    {{-- Contoh struktur tab:
                    <div class="tab-pane fade" id="addresses" role="tabpanel" aria-labelledby="addresses-tab">
                        <div class="card shadow-lg p-5 rounded-3">
                            <h3 class="h4 mb-4">Alamat Tersimpan</h3>
                            <p>Konten untuk mengelola alamat.</p>
                            <a href="#" class="btn btn-primary rounded-pill">Kelola Alamat</a>
                        </div>
                    </div>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');
        const errorsExist = @json($errors->any());

        if (errorsExist) {
            const errorFields = @json($errors->keys());
            if (errorFields.includes('current_password') || errorFields.includes('new_password') || errorFields.includes('new_password_confirmation')) {
                // Error terkait password, aktifkan tab password
                new bootstrap.Tab(document.getElementById('password-tab')).show();
            } else if (errorFields.includes('name') || errorFields.includes('email') || errorFields.includes('phone_number') || errorFields.includes('address')) {
                // Error terkait profil, aktifkan tab profil
                new bootstrap.Tab(document.getElementById('profile-tab')).show();
            } else {
                // Default ke tab overview jika tidak ada error spesifik atau error lain
                new bootstrap.Tab(document.getElementById('overview-tab')).show();
            }
        } else if (activeTab) {
            // Jika ada parameter 'tab' di URL, aktifkan tab tersebut
            const tabElement = document.getElementById(activeTab + '-tab');
            if (tabElement) {
                new bootstrap.Tab(tabElement).show();
            }
        } else {
            new bootstrap.Tab(document.getElementById('overview-tab')).show();
        }

        @if(session('success'))
            const successMessage = "{{ session('success') }}";
            if (successMessage.includes('Password berhasil diubah')) {
                new bootstrap.Tab(document.getElementById('password-tab')).show();
            } else if (successMessage.includes('Informasi pribadi berhasil diperbarui')) {
                new bootstrap.Tab(document.getElementById('profile-tab')).show();
            }
        @endif
    });
</script>
@endpush
