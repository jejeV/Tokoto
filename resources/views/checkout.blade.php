@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<section class="wrapper bg-light">
    <div class="container pt-12 pt-md-14 pb-14 pb-md-16">
        <div class="row gx-md-8 gx-xl-12 gy-12">
            <div class="col-lg-8">
                <h1 class="display-5 text-center mb-6">Checkout</h1>

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

                @push('head_meta')
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                @endpush

                <form id="checkout-form" class="needs-validation" novalidate action="{{ route('checkout.process') }}" method="POST">
                    @csrf

                    @guest
                        <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                            <i class="uil uil-info-circle me-2"></i> Sudah punya akun? <a href="{{ route('login') }}" class="alert-link ms-1">Masuk</a> untuk pengalaman checkout yang lebih cepat.
                        </div>
                    @endguest

                    {{-- Billing Address Section (Selalu Tampilkan form, pra-isi jika user login) --}}
                    <div class="card card-body shadow-lg mb-5">
                        <h4 class="card-title text-center mb-4">Alamat Penagihan</h4>

                        <div id="billing_address_form" class="row gy-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
                                    <input id="billing_first_name" type="text" name="billing_first_name" class="form-control @error('billing_first_name') is-invalid @enderror" placeholder="Nama Depan" value="{{ old('billing_first_name', $user->first_name ?? '') }}" required>
                                    <label for="billing_first_name">Nama Depan*</label>
                                    @error('billing_first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
                                    <input id="billing_last_name" type="text" name="billing_last_name" class="form-control @error('billing_last_name') is-invalid @enderror" placeholder="Nama Belakang" value="{{ old('billing_last_name', $user->last_name ?? '') }}">
                                    <label for="billing_last_name">Nama Belakang</label> {{-- Tidak wajib jika tidak diisi --}}
                                    @error('billing_last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating mb-4">
                                    <input id="billing_email" type="email" name="billing_email" class="form-control @error('billing_email') is-invalid @enderror" placeholder="Email" value="{{ old('billing_email', $user->email ?? '') }}" required>
                                    <label for="billing_email">Email*</label>
                                    @error('billing_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating mb-4">
                                    <input id="billing_address_line_1" type="text" name="billing_address_line_1" class="form-control @error('billing_address_line_1') is-invalid @enderror" placeholder="Alamat Baris 1" value="{{ old('billing_address_line_1', $user->address_line_1 ?? '') }}" required>
                                    <label for="billing_address_line_1">Alamat Baris 1*</label>
                                    @error('billing_address_line_1')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating mb-4">
                                    <input id="billing_address_line_2" type="text" name="billing_address_line_2" class="form-control @error('billing_address_line_2') is-invalid @enderror" placeholder="Alamat Baris 2 (Opsional)" value="{{ old('billing_address_line_2', $user->address_line_2 ?? '') }}">
                                    <label for="billing_address_line_2">Alamat Baris 2 (Opsional)</label>
                                    @error('billing_address_line_2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
                                    <select class="form-select @error('billing_province_id') is-invalid @enderror" id="billing_province_id" name="billing_province_id" required>
                                        <option value="">Pilih Provinsi</option>
                                        @foreach($provinces as $province)
                                            <option value="{{ $province->id }}" {{ old('billing_province_id', $user->province_id ?? '') == $province->id ? 'selected' : '' }}>{{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="billing_province_id">Provinsi*</label>
                                    @error('billing_province_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
                                    <select class="form-select @error('billing_city_id') is-invalid @enderror" id="billing_city_id" name="billing_city_id" required>
                                        <option value="">Pilih Kota</option>
                                        {{-- Initial cities for billing form, pre-filled if old input or user data exists --}}
                                        @if(!empty(old('billing_province_id', $user->province_id ?? '')) && !$citiesForBillingForm->isEmpty())
                                            @foreach($citiesForBillingForm as $city)
                                                <option value="{{ $city->id }}" {{ old('billing_city_id', $user->city_id ?? '') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                            @endforeach
                                        @elseif (!empty(old('billing_province_id', $user->province_id ?? '')))
                                            {{-- Jika provinsi dipilih tapi tidak ada kota (misal: data tidak konsisten atau provinsi baru) --}}
                                            <option value="" disabled>Tidak ada kota ditemukan untuk provinsi ini</option>
                                        @endif
                                    </select>
                                    <label for="billing_city_id">Kota*</label>
                                    @error('billing_city_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
                                    <input id="billing_zip_code" type="text" name="billing_zip_code" class="form-control @error('billing_zip_code') is-invalid @enderror" placeholder="Kode Pos" value="{{ old('billing_zip_code', $user->zip_code ?? '') }}" required>
                                    <label for="billing_zip_code">Kode Pos*</label>
                                    @error('billing_zip_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
                                    <input id="billing_phone_number" type="text" name="billing_phone_number" class="form-control @error('billing_phone_number') is-invalid @enderror" placeholder="Nomor Telepon" value="{{ old('billing_phone_number', $user->phone_number ?? '') }}" required>
                                    <label for="billing_phone_number">Nomor Telepon*</label>
                                    @error('billing_phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    {{-- Checkbox ini hanya relevan untuk user yang login untuk update profil mereka --}}
                                    <input class="form-check-input" type="checkbox" id="save_address_for_next_time" name="save_address_for_next_time" {{ (Auth::check() && (old('save_address_for_next_time') || ($user->address_line_1 && $user->province_id && $user->city_id))) ? 'checked' : (old('save_address_for_next_time') ? 'checked' : '') }}>
                                    <label class="form-check-label" for="save_address_for_next_time">
                                        Simpan informasi ini untuk lain kali
                                        @guest
                                        <small class="text-muted">(Akan disimpan di profil Anda setelah pendaftaran)</small>
                                        @endguest
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Shipping Address Section --}}
                    <div class="card card-body shadow-lg mb-5">
                        <h4 class="card-title text-center mb-4">Alamat Pengiriman</h4>
                        <div class="form-check mb-4">
                            {{-- Gunakan old('same_as_billing') untuk menjaga state checkbox setelah validasi gagal --}}
                            <input class="form-check-input" type="checkbox" id="same_as_billing" name="same_as_billing" {{ old('same_as_billing', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="same_as_billing">
                                Alamat pengiriman sama dengan alamat penagihan
                            </label>
                        </div>

                        {{-- Bagian ini akan disembunyikan secara default atau ditampilkan berdasarkan old('same_as_billing') --}}
                        <div id="shipping_address_form" class="row gy-3" style="display: {{ old('same_as_billing', true) ? 'none' : 'block' }};">
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
                                    {{-- Gunakan old() untuk mengisi kembali jika validasi gagal dan form shipping ditampilkan --}}
                                    <input id="shipping_first_name" type="text" name="shipping_first_name" class="form-control @error('shipping_first_name') is-invalid @enderror" placeholder="Nama Depan" value="{{ old('shipping_first_name', $user->first_name ?? '') }}" @if(old('same_as_billing', true) == false) required @endif>
                                    <label for="shipping_first_name">Nama Depan*</label>
                                    @error('shipping_first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
                                    <input id="shipping_last_name" type="text" name="shipping_last_name" class="form-control @error('shipping_last_name') is-invalid @enderror" placeholder="Nama Belakang" value="{{ old('shipping_last_name', $user->last_name ?? '') }}" @if(old('same_as_billing', true) == false) required @endif>
                                    <label for="shipping_last_name">Nama Belakang*</label>
                                    @error('shipping_last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating mb-4">
                                    <input id="shipping_email" type="email" name="shipping_email" class="form-control @error('shipping_email') is-invalid @enderror" placeholder="Email" value="{{ old('shipping_email', $user->email ?? '') }}" @if(old('same_as_billing', true) == false) required @endif>
                                    <label for="shipping_email">Email*</label>
                                    @error('shipping_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating mb-4">
                                    <input id="shipping_address_line_1" type="text" name="shipping_address_line_1" class="form-control @error('shipping_address_line_1') is-invalid @enderror" placeholder="Alamat Baris 1" value="{{ old('shipping_address_line_1', $user->address_line_1 ?? '') }}" @if(old('same_as_billing', true) == false) required @endif>
                                    <label for="shipping_address_line_1">Alamat Baris 1*</label>
                                    @error('shipping_address_line_1')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating mb-4">
                                    <input id="shipping_address_line_2" type="text" name="shipping_address_line_2" class="form-control @error('shipping_address_line_2') is-invalid @enderror" placeholder="Alamat Baris 2 (Opsional)" value="{{ old('shipping_address_line_2', $user->address_line_2 ?? '') }}">
                                    <label for="shipping_address_line_2">Alamat Baris 2 (Opsional)</label>
                                    @error('shipping_address_line_2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
                                    <select class="form-select @error('shipping_province_id') is-invalid @enderror" id="shipping_province_id" name="shipping_province_id" @if(old('same_as_billing', true) == false) required @endif>
                                        <option value="">Pilih Provinsi</option>
                                        @foreach($provinces as $province)
                                            <option value="{{ $province->id }}" {{ old('shipping_province_id', $user->province_id ?? '') == $province->id ? 'selected' : '' }}>{{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="shipping_province_id">Provinsi*</label>
                                    @error('shipping_province_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
                                    <select class="form-select @error('shipping_city_id') is-invalid @enderror" id="shipping_city_id" name="shipping_city_id" @if(old('same_as_billing', true) == false) required @endif>
                                        <option value="">Pilih Kota</option>
                                        {{-- Initial cities for shipping form, pre-filled if old input or user data exists and "same_as_billing" is NOT checked --}}
                                        @if(!empty(old('shipping_province_id', $user->province_id ?? '')) && !old('same_as_billing', true) && !$citiesForShippingForm->isEmpty())
                                            @foreach($citiesForShippingForm as $city)
                                                <option value="{{ $city->id }}" {{ old('shipping_city_id', $user->city_id ?? '') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                            @endforeach
                                        @elseif (!empty(old('shipping_province_id', $user->province_id ?? '')) && !old('same_as_billing', true))
                                            {{-- Jika provinsi dipilih tapi tidak ada kota --}}
                                            <option value="" disabled>Tidak ada kota ditemukan untuk provinsi ini</option>
                                        @endif
                                    </select>
                                    <label for="shipping_city_id">Kota*</label>
                                    @error('shipping_city_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
                                    <input id="shipping_zip_code" type="text" name="shipping_zip_code" class="form-control @error('shipping_zip_code') is-invalid @enderror" placeholder="Kode Pos" value="{{ old('shipping_zip_code', $user->zip_code ?? '') }}" @if(old('same_as_billing', true) == false) required @endif>
                                    <label for="shipping_zip_code">Kode Pos*</label>
                                    @error('shipping_zip_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
                                    <input id="shipping_phone_number" type="text" name="shipping_phone_number" class="form-control @error('shipping_phone_number') is-invalid @enderror" placeholder="Nomor Telepon" value="{{ old('shipping_phone_number', $user->phone_number ?? '') }}" @if(old('same_as_billing', true) == false) required @endif>
                                    <label for="shipping_phone_number">Nomor Telepon*</label>
                                    @error('shipping_phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Shipping Options --}}
                    <div class="card card-body shadow-lg mt-5">
                        <h4 class="card-title text-center mb-4">Metode Pengiriman</h4>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="shipping_method" id="shipping_standard" value="standard" data-cost="0" {{ old('shipping_method', 'standard') == 'standard' ? 'checked' : '' }}>
                            <label class="form-check-label" for="shipping_standard">
                                Gratis - Pengiriman Standar
                                <span class="d-block text-muted text-sm">Pengiriman dapat memakan waktu 5-6 hari kerja</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="shipping_method" id="shipping_express" value="express" data-cost="10000" {{ old('shipping_method') == 'express' ? 'checked' : '' }}> {{-- Contoh biaya dalam IDR --}}
                            <label class="form-check-label" for="shipping_express">
                                Rp. 10.000 - Pengiriman Ekspres
                                <span class="d-block text-muted text-sm">Pengiriman dapat memakan waktu 2-3 hari kerja</span>
                            </label>
                        </div>
                        @error('shipping_method')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Payment Method (Midtrans will handle the actual CC/Bank selection) --}}
                    <div class="card card-body shadow-lg mt-5">
                        <h4 class="card-title text-center mb-4">Metode Pembayaran</h4>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="midtrans" value="midtrans" checked>
                            <label class="form-check-label" for="midtrans">
                                Midtrans (Kartu Kredit, GoPay, Transfer Bank, dll.)
                            </label>
                        </div>
                        @error('payment_method')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill" id="pay-button">Tempatkan Pesanan & Bayar</button>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <h3 class="mb-4">Ringkasan Pesanan</h3>
                <div class="table-responsive">
                    <table class="table table-order">
                        <thead>
                            <tr>
                                <th class="ps-0">Produk</th>
                                <th class="pe-0 text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody id="order-summary-items">
                            @foreach($cartItems as $item)
                            <tr>
                                <td class="ps-0">
                                    {{ $item['name'] }} ({{ $item['quantity'] }}x)
                                    @if(isset($item['selected_size']) && $item['selected_size'])
                                        <br><small class="text-muted">Size: {{ $item['selected_size'] }}</small>
                                    @endif
                                    @if(isset($item['selected_color']) && $item['selected_color'])
                                        <br><small class="text-muted">Color: {{ $item['selected_color'] }}</small>
                                    @endif
                                </td>
                                <td class="pe-0 text-end">Rp. {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tbody>
                            <tr>
                                <td class="ps-0"><strong class="text-dark">Subtotal</strong></td>
                                <td class="pe-0 text-end">
                                    <p class="price" id="order-subtotal">Rp. {{ number_format($cartSubtotal, 0, ',', '.') }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-0"><strong class="text-dark">Pengiriman</strong></td>
                                <td class="pe-0 text-end">
                                    {{-- Menggunakan nilai old jika ada, atau default ke 0 --}}
                                    <p class="price" id="order-shipping">Rp. {{ number_format(old('shipping_method', 'standard') == 'express' ? 10000 : 0, 0, ',', '.') }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-0"><strong class="text-dark">Total Akhir</strong></td>
                                <td class="pe-0 text-end">
                                    {{-- Menggunakan nilai old untuk perhitungan total awal --}}
                                    <p class="price text-dark fw-bold" id="order-grand-total">Rp. {{ number_format($cartSubtotal + (old('shipping_method', 'standard') == 'express' ? 10000 : 0), 0, ',', '.') }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
{{-- Midtrans Snap JS Library --}}
<script type="text/javascript"
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
    // Konstan dari PHP ke JavaScript
    const cartSubtotal = {{ $cartSubtotal }};
    // Mengambil biaya pengiriman awal dari radio button yang tercentang di Blade
    let currentShippingCost = parseFloat(document.querySelector('input[name="shipping_method"]:checked').dataset.cost || 0);

    // Fungsi untuk memformat mata uang ke IDR Rupiah
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }

    // Fungsi untuk memperbarui bagian Ringkasan Pesanan
    function updateOrderSummary() {
        const grandTotal = cartSubtotal + currentShippingCost;
        document.getElementById('order-subtotal').innerText = formatRupiah(cartSubtotal);
        document.getElementById('order-shipping').innerText = formatRupiah(currentShippingCost);
        document.getElementById('order-grand-total').innerText = formatRupiah(grandTotal);
    }

    // Pembaruan biaya pengiriman dinamis
    document.querySelectorAll('input[name="shipping_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            currentShippingCost = this.dataset.cost ? parseFloat(this.dataset.cost) : 0;
            updateOrderSummary();
        });
    });

    // --- Logika Form Alamat ---
    const sameAsBillingCheckbox = document.getElementById('same_as_billing');
    const shippingAddressForm = document.getElementById('shipping_address_form');

    // Helper untuk mengatur atribut 'required' untuk semua elemen form di dalam div
    function setShippingFormFieldsRequired(isRequired) {
        const fields = [
            'shipping_first_name', 'shipping_last_name', 'shipping_email',
            'shipping_address_line_1', 'shipping_province_id', 'shipping_city_id',
            'shipping_zip_code', 'shipping_phone_number'
        ];
        fields.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                if (isRequired) {
                    el.setAttribute('required', 'required');
                    // Tambahkan kembali kelas 'is-invalid' jika ada error dari old input
                    if (el.classList.contains('is-invalid-temp')) { // Gunakan kelas temp untuk identifikasi error lama
                        el.classList.add('is-invalid');
                        el.classList.remove('is-invalid-temp');
                    }
                } else {
                    el.removeAttribute('required');
                    // Hapus kelas 'is-invalid' dan simpan di 'is-invalid-temp' untuk kemungkinan pengembalian
                    if (el.classList.contains('is-invalid')) {
                        el.classList.add('is-invalid-temp');
                        el.classList.remove('is-invalid');
                        // Hapus juga pesan error di bawahnya jika ada
                        const invalidFeedback = el.nextElementSibling;
                        if (invalidFeedback && invalidFeedback.classList.contains('invalid-feedback')) {
                            invalidFeedback.style.display = 'none'; // Sembunyikan pesan error
                        }
                    }
                }
            }
        });
    }

    // Fungsi untuk menyalin data dari form billing ke form shipping
    function copyBillingToShipping() {
        document.getElementById('shipping_first_name').value = document.getElementById('billing_first_name').value;
        document.getElementById('shipping_last_name').value = document.getElementById('billing_last_name').value;
        document.getElementById('shipping_email').value = document.getElementById('billing_email').value;
        document.getElementById('shipping_address_line_1').value = document.getElementById('billing_address_line_1').value;
        document.getElementById('shipping_address_line_2').value = document.getElementById('billing_address_line_2').value;
        document.getElementById('shipping_zip_code').value = document.getElementById('billing_zip_code').value;
        document.getElementById('shipping_phone_number').value = document.getElementById('billing_phone_number').value;

        // Copy province and trigger city load
        const billingProvinceId = document.getElementById('billing_province_id').value;
        const billingCityId = document.getElementById('billing_city_id').value;
        document.getElementById('shipping_province_id').value = billingProvinceId;

        // Load cities for shipping based on billing province
        if (billingProvinceId) {
            loadCities('shipping_province_id', 'shipping_city_id', billingCityId);
        } else {
            document.getElementById('shipping_city_id').innerHTML = '<option value="">Pilih Kota</option>';
        }
    }

    // Fungsi untuk mengambil dan mengisi dropdown kota
    async function loadCities(provinceSelectId, citySelectId, selectedCityId = null) {
        const provinceSelect = document.getElementById(provinceSelectId);
        const citySelect = document.getElementById(citySelectId);
        const provinceId = provinceSelect.value;

        // Clear previous options and add default
        citySelect.innerHTML = '<option value="">Memuat Kota...</option>';
        citySelect.disabled = true;

        // Hapus kelas is-invalid jika ada pada dropdown kota yang akan diisi
        citySelect.classList.remove('is-invalid');
        const invalidFeedback = citySelect.nextElementSibling;
        if (invalidFeedback && invalidFeedback.classList.contains('invalid-feedback')) {
            invalidFeedback.style.display = 'none';
        }


        if (provinceId) {
            try {
                const response = await fetch(`/api/cities/${provinceId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                const cities = await response.json();

                citySelect.innerHTML = '<option value="">Pilih Kota</option>'; // Reset
                if (cities.length > 0) {
                    cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.id;
                        option.innerText = city.name;
                        citySelect.appendChild(option);
                    });
                    // Set selected city if provided (e.g., from old input or user data)
                    if (selectedCityId) {
                        citySelect.value = selectedCityId;
                    }
                } else {
                    citySelect.innerHTML = '<option value="" disabled>Tidak ada kota ditemukan untuk provinsi ini</option>';
                }
                citySelect.disabled = false;

            } catch (error) {
                console.error('Error loading cities:', error);
                citySelect.innerHTML = '<option value="" disabled>Gagal memuat kota</option>';
                citySelect.disabled = true;
            }
        } else {
            citySelect.innerHTML = '<option value="">Pilih Kota</option>';
            citySelect.disabled = false; // Enable if no province selected, but no cities are there
        }
    }

    // Event listener untuk checkbox "Alamat pengiriman sama dengan penagihan"
    sameAsBillingCheckbox.addEventListener('change', function() {
        if (this.checked) {
            shippingAddressForm.style.display = 'none';
            setShippingFormFieldsRequired(false);
            copyBillingToShipping(); // Salin data billing ke shipping
        } else {
            shippingAddressForm.style.display = 'block';
            setShippingFormFieldsRequired(true);
            // Saat tidak dicentang, kita tidak menyalin otomatis.
            // Data akan diisi dari old input atau data user yang disimpan sebelumnya.
            // Jika ada error validasi untuk shipping, biarkan muncul.
            // Panggil loadCities untuk shipping form jika ada provinceId yang diisi dari old/user data.
            const shippingProvinceId = document.getElementById('shipping_province_id').value;
            const shippingCityId = "{{ old('shipping_city_id', $user->city_id ?? '') }}";
            if (shippingProvinceId) {
                loadCities('shipping_province_id', 'shipping_city_id', shippingCityId);
            }
        }
    });

    // Event listener untuk perubahan provinsi di Billing
    document.getElementById('billing_province_id').addEventListener('change', function() {
        const selectedCityId = "{{ old('billing_city_id', $user->city_id ?? '') }}";
        loadCities('billing_province_id', 'billing_city_id', this.value === "{{ old('billing_province_id', $user->province_id ?? '') }}" ? selectedCityId : null);

        // Jika "Same as billing" dicentang, salin juga ke shipping saat provinsi billing berubah
        if (sameAsBillingCheckbox.checked) {
            copyBillingToShipping();
        }
    });

    // Event listener untuk perubahan provinsi di Shipping (hanya jika "Same as billing" TIDAK dicentang)
    document.getElementById('shipping_province_id').addEventListener('change', function() {
        if (!sameAsBillingCheckbox.checked) {
            const selectedCityId = "{{ old('shipping_city_id', $user->city_id ?? '') }}";
            loadCities('shipping_province_id', 'shipping_city_id', this.value === "{{ old('shipping_province_id', $user->province_id ?? '') }}" ? selectedCityId : null);
        }
    });


    // --- Inisialisasi Saat Halaman Dimuat ---
    document.addEventListener('DOMContentLoaded', function() {
        updateOrderSummary();

        // Inisialisasi dropdown kota billing
        const initialBillingProvinceId = document.getElementById('billing_province_id').value;
        const initialBillingCityId = "{{ old('billing_city_id', $user->city_id ?? '') }}";
        if (initialBillingProvinceId) {
            loadCities('billing_province_id', 'billing_city_id', initialBillingCityId);
        }

        // Inisialisasi visibility dan required fields untuk shipping address form
        // Ini memastikan form shipping diatur dengan benar berdasarkan old('same_as_billing')
        // saat halaman dimuat (terutama setelah validasi gagal).
        if (!sameAsBillingCheckbox.checked) {
            shippingAddressForm.style.display = 'block';
            setShippingFormFieldsRequired(true);
            const initialShippingProvinceId = document.getElementById('shipping_province_id').value;
            const initialShippingCityId = "{{ old('shipping_city_id', $user->city_id ?? '') }}";
             if (initialShippingProvinceId) {
                loadCities('shipping_province_id', 'shipping_city_id', initialShippingCityId);
            }
        } else {
            shippingAddressForm.style.display = 'none';
            setShippingFormFieldsRequired(false);
            // Jika initial load dan same_as_billing tercentang, salin data
            copyBillingToShipping();
        }

        // Bootstrap form validation
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    });
</script>
@endsection
