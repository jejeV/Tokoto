@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<section class="wrapper bg-light">
    <div class="container pt-12 pt-md-14 pb-14 pb-md-16">
        <div class="row gx-md-8 gx-xl-12 gy-12">
            <div class="col-lg-8">
                <h1 class="display-5 text-center mb-6">Checkout</h1>
                <div id="alert-container" class="mb-4"></div>

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

                {{-- Error validasi dari backend Laravel (untuk kasus non-AJAX atau fallback) --}}
                {{-- Saat menggunakan AJAX, ini akan disembunyikan oleh JavaScript --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show backend-validation-errors" role="alert">
                        Terjadi kesalahan validasi. Mohon periksa kembali input Anda.
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Loading indicator --}}
                <div id="loading-indicator" class="alert alert-info text-center" style="display: none;">
                    <div class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></div>
                    Memproses pesanan Anda...
                </div>

                {{-- FORM INI AKAN DISUBMIT SECARA AJAX --}}
                <form id="checkout-form" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @guest
                        <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                            <i class="uil uil-info-circle me-2"></i> Sudah punya akun? <a href="{{ route('login') }}" class="alert-link ms-1">Masuk</a> untuk pengalaman checkout yang lebih cepat.
                        </div>
                    @endguest

                    <div class="card card-body shadow-lg mb-5">
                        <h4 class="card-title text-center mb-4">Detail Alamat (Penagihan & Pengiriman)</h4>
                        <div class="row gy-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
                                    {{-- ID dan Name disesuaikan menjadi 'first_name' --}}
                                    <input id="first_name" type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" placeholder="Nama Depan" value="{{ old('first_name', $user->first_name ?? '') }}" required>
                                    <label for="first_name">Nama Depan*</label>
                                    <div class="invalid-feedback">Nama depan harus diisi.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
                                    {{-- ID dan Name disesuaikan menjadi 'last_name' --}}
                                    <input id="last_name" type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" placeholder="Nama Belakang" value="{{ old('last_name', $user->last_name ?? '') }}">
                                    <label for="last_name">Nama Belakang</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating mb-4">
                                    {{-- ID dan Name disesuaikan menjadi 'email' --}}
                                    <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email', $user->email ?? '') }}" required>
                                    <label for="email">Email*</label>
                                    <div class="invalid-feedback">Email harus diisi dan valid.</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating mb-4">
                                    {{-- ID dan Name disesuaikan menjadi 'phone' --}}
                                    <input id="phone" type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Nomor Telepon" value="{{ old('phone', $user->phone_number ?? '') }}" required>
                                    <label for="phone">Nomor Telepon*</label>
                                    <div class="invalid-feedback">Nomor telepon harus diisi.</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating mb-4">
                                    {{-- ID dan Name disesuaikan menjadi 'address_line1' --}}
                                    <input id="address_line1" type="text" name="address_line1" class="form-control @error('address_line1') is-invalid @enderror" placeholder="Alamat Baris 1" value="{{ old('address_line1', $user->address ?? '') }}" required>
                                    <label for="address_line1">Alamat Baris 1*</label>
                                    <div class="invalid-feedback">Alamat harus diisi.</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating mb-4">
                                    {{-- ID dan Name disesuaikan menjadi 'address_line2' --}}
                                    <input id="address_line2" type="text" name="address_line2" class="form-control @error('address_line2') is-invalid @enderror" placeholder="Alamat Baris 2 (Opsional)" value="{{ old('address_line2', '') }}">
                                    <label for="address_line2">Alamat Baris 2 (Opsional)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
                                    {{-- ID dan Name disesuaikan menjadi 'province' --}}
                                    <input id="province" type="text" name="province" class="form-control @error('province') is-invalid @enderror" placeholder="Provinsi" value="{{ old('province', $user->province->name ?? '') }}" required>
                                    <label for="province">Provinsi*</label>
                                    <div class="invalid-feedback">Provinsi harus diisi.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
                                    {{-- ID dan Name disesuaikan menjadi 'city' --}}
                                    <input id="city" type="text" name="city" class="form-control @error('city') is-invalid @enderror" placeholder="Kota" value="{{ old('city', $user->city->name ?? '') }}" required>
                                    <label for="city">Kota*</label>
                                    <div class="invalid-feedback">Kota harus diisi.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
                                    {{-- ID dan Name disesuaikan menjadi 'zip_code' --}}
                                    <input id="zip_code" type="text" name="zip_code" class="form-control @error('zip_code') is-invalid @enderror" placeholder="Kode Pos" value="{{ old('zip_code', $user->zip_code ?? '') }}" required>
                                    <label for="zip_code">Kode Pos*</label>
                                    <div class="invalid-feedback">Kode pos harus diisi.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Shipping Method --}}
                    <div class="card card-body shadow-lg mt-5">
                        <h4 class="card-title text-center mb-4">Metode Pengiriman</h4>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="shipping_method" id="shipping_standard" value="Pengiriman Standar" data-cost="0" {{ old('shipping_method', 'Pengiriman Standar') == 'Pengiriman Standar' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="shipping_standard">
                                Gratis - Pengiriman Standar
                                <span class="d-block text-muted text-sm">Pengiriman dapat memakan waktu 5-6 hari kerja</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="shipping_method" id="shipping_express" value="Pengiriman Ekspres" data-cost="10000" {{ old('shipping_method') == 'Pengiriman Ekspres' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="shipping_express">
                                Rp. 10.000 - Pengiriman Ekspres
                                <span class="d-block text-muted text-sm">Pengiriman dapat memakan waktu 2-3 hari kerja</span>
                            </label>
                        </div>
                        <div class="invalid-feedback">Pilih metode pengiriman.</div>
                        @error('shipping_method')
                            <div class="text-danger small mt-2 d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="card card-body shadow-lg mt-5">
                        <h4 class="card-title text-center mb-4">Metode Pembayaran</h4>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="midtrans" value="midtrans" {{ old('payment_method', 'midtrans') == 'midtrans' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="midtrans">
                                Midtrans (Kartu Kredit, GoPay, Transfer Bank, dll.)
                            </label>
                        </div>
                        <div class="invalid-feedback">Pilih metode pembayaran.</div>
                        @error('payment_method')
                            <div class="text-danger small mt-2 d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill" id="place-order-button">
                            <span class="btn-text">Tempatkan Pesanan & Bayar</span>
                            <span class="btn-loading d-none">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                Memproses...
                            </span>
                        </button>
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
                            {{-- Menggunakan $checkoutItems yang disiapkan di controller --}}
                            @forelse($checkoutItems as $item)
                            <tr>
                                <td class="ps-0">
                                    {{ $item['name'] }} ({{ $item['quantity'] }}x)
                                </td>
                                <td class="pe-0 text-end">Rp. {{ number_format($item['total_price'], 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center">Keranjang belanja kosong.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tbody>
                            <tr>
                                <td class="ps-0"><strong class="text-dark">Subtotal</strong></td>
                                <td class="pe-0 text-end">
                                    {{-- Pastikan $cartSubtotal selalu ada dari controller --}}
                                    <p class="price" id="order-subtotal">Rp. {{ number_format($cartSubtotal ?? 0, 0, ',', '.') }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-0"><strong class="text-dark">Pengiriman</strong></td>
                                <td class="pe-0 text-end">
                                    {{-- Mengambil nilai default dari shipping_method yang dipilih jika ada --}}
                                    <p class="price" id="order-shipping">Rp. {{ number_format(old('shipping_method') == 'Pengiriman Ekspres' ? 10000 : 0, 0, ',', '.') }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-0"><strong class="text-dark">Total Akhir</strong></td>
                                <td class="pe-0 text-end">
                                    {{-- Menghitung total akhir berdasarkan subtotal dan biaya pengiriman yang dipilih --}}
                                    <p class="price text-dark fw-bold" id="order-grand-total">Rp. {{ number_format(($cartSubtotal ?? 0) + (old('shipping_method') == 'Pengiriman Ekspres' ? 10000 : 0), 0, ',', '.') }}</p>
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

@push('scripts')
<script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
jQuery(document).ready(function($) { // $ di sini akan merujuk ke jQuery
    const checkoutForm = document.getElementById('checkout-form');
    const placeOrderButton = document.getElementById('place-order-button');
    const loadingIndicator = document.getElementById('loading-indicator');
    const btnText = placeOrderButton.querySelector('.btn-text');
    const btnLoading = placeOrderButton.querySelector('.btn-loading');

    // Fungsi untuk menampilkan loading state
    function showLoading() {
        placeOrderButton.disabled = true;
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');
        loadingIndicator.style.display = 'block';
        const backendErrors = document.querySelector('.backend-validation-errors');
        if (backendErrors) {
            backendErrors.style.display = 'none';
        }
    }

    // Fungsi untuk menyembunyikan loading state
    function hideLoading() {
        placeOrderButton.disabled = false;
        btnText.classList.remove('d-none');
        btnLoading.classList.add('d-none');
        loadingIndicator.style.display = 'none';
    }

    // Fungsi untuk menampilkan error AJAX (alert di atas form)
    function showError(message) {
        const existingAlert = document.querySelector('#alert-container .alert'); // Ubah selector
        if (existingAlert) {
            existingAlert.remove();
        }

        const alertDiv = `<div class="alert alert-danger alert-dismissible fade show alert-checkout-error" role="alert">
                            ${message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>`;
        document.getElementById('alert-container').insertAdjacentHTML('afterbegin', alertDiv); // Masukkan ke alert-container
        document.getElementById('alert-container').scrollIntoView({ behavior: 'smooth', block: 'center' }); // Scroll ke alert
    }

    // Fungsi untuk menampilkan error validasi per field dari respons JSON
    function displayValidationErrors(errors) {
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback.d-block').forEach(el => {
            el.remove();
        });

        for (const fieldName in errors) {
            let inputField = document.getElementById(fieldName) || document.querySelector(`[name="${fieldName}"]`);

            if (inputField) {
                inputField.classList.add('is-invalid');
                const errorMessage = errors[fieldName].join('<br>');
                const feedbackDiv = document.createElement('div');
                feedbackDiv.className = 'invalid-feedback d-block';
                feedbackDiv.innerHTML = errorMessage;

                if (inputField.nextElementSibling && inputField.nextElementSibling.classList.contains('invalid-feedback')) {
                    inputField.nextElementSibling.remove();
                }
                inputField.parentNode.appendChild(feedbackDiv);

                if (!document.querySelector('.is-invalid:focus')) {
                    inputField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    inputField.focus();
                }
            }
        }
        showError('Terjadi kesalahan validasi. Mohon perbaiki input Anda.'); // Tampilkan juga alert umum di atas
    }

    $('#checkout-form').on('submit', function(e) {
        e.preventDefault();

        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback.d-block').remove();
        $('#alert-container').empty(); // Kosongkan alert container
        $('.backend-validation-errors').hide();

        if (!checkoutForm.checkValidity()) {
            e.stopPropagation();
            checkoutForm.classList.add('was-validated');
            const firstInvalidField = checkoutForm.querySelector(':invalid');
            if (firstInvalidField) {
                firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalidField.focus();
            }
            showError('Mohon lengkapi semua field yang wajib diisi.'); // Notifikasi umum jika validasi HTML5 gagal
            return;
        }

        showLoading();

        const formData = $(this).serialize();

        $.ajax({
            method: "POST",
            url: "{{ route('checkout.process')}}",
            data: formData,
            success: function(data) {
                hideLoading();
                console.log('AJAX Success:', data);

                if (data.success && data.snap_token) {
                    snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            console.log('Payment Success:', result);
                            window.location.href = "{{ route('checkout.success') }}?order_id=" + data.order_id;
                        },
                        onPending: function(result) {
                            console.log('Payment Pending:', result);
                            window.location.href = "{{ route('checkout.pending') }}?order_id=" + data.order_id;
                        },
                        onError: function(result) {
                            console.log('Payment Error:', result);
                            showError('Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
                        },
                        onClose: function() {
                            console.log('Payment popup closed');
                            showError('Pembayaran dibatalkan atau ditutup oleh pengguna.');
                        }
                    });
                } else if (data.errors) {
                    displayValidationErrors(data.errors);
                } else {
                    showError(data.message || 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                hideLoading();
                console.error('AJAX Error:', textStatus, errorThrown, jqXHR);

                if (jqXHR.responseJSON && jqXHR.responseJSON.errors) {
                    displayValidationErrors(jqXHR.responseJSON.errors);
                } else if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                    showError(jqXHR.responseJSON.message);
                } else {
                    showError('Terjadi kesalahan jaringan atau server. Silakan coba lagi.');
                }
            }
        });
    });

    const shippingRadios = document.querySelectorAll('input[name="shipping_method"]');
    const orderSubtotalElement = document.getElementById('order-subtotal');
    const orderShippingElement = document.getElementById('order-shipping');
    const orderGrandTotalElement = document.getElementById('order-grand-total');

    function updateOrderSummary() {
        let selectedShippingCost = 0;
        shippingRadios.forEach(radio => {
            if (radio.checked) {
                selectedShippingCost = parseFloat(radio.dataset.cost);
            }
        });

        const subtotalText = orderSubtotalElement.textContent.replace('Rp. ', '').replace(/\./g, '');
        const currentSubtotal = parseFloat(subtotalText);

        const newGrandTotal = currentSubtotal + selectedShippingCost;

        orderShippingElement.textContent = 'Rp. ' + new Intl.NumberFormat('id-ID').format(selectedShippingCost);
        orderGrandTotalElement.textContent = 'Rp. ' + new Intl.NumberFormat('id-ID').format(newGrandTotal);
    }

    shippingRadios.forEach(radio => {
        radio.addEventListener('change', updateOrderSummary);
    });

    updateOrderSummary();
});
</script>
@endpush
