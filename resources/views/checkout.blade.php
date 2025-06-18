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
                                    <input id="first_name" type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" placeholder="Nama Depan" value="{{ old('first_name', $user->first_name ?? '') }}" required>
                                    <label for="first_name">Nama Depan*</label>
                                    <div class="invalid-feedback">Nama depan harus diisi.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
                                    <input id="last_name" type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" placeholder="Nama Belakang" value="{{ old('last_name', $user->last_name ?? '') }}">
                                    <label for="last_name">Nama Belakang</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating mb-4">
                                    <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email', $user->email ?? '') }}" required>
                                    <label for="email">Email*</label>
                                    <div class="invalid-feedback">Email harus diisi dan valid.</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating mb-4">
                                    <input id="phone" type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Nomor Telepon" value="{{ old('phone', $user->phone_number ?? '') }}" required>
                                    <label for="phone">Nomor Telepon*</label>
                                    <div class="invalid-feedback">Nomor telepon harus diisi.</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating mb-4">
                                    <input id="address_line1" type="text" name="address_line1" class="form-control @error('address_line1') is-invalid @enderror" placeholder="Alamat Baris 1" value="{{ old('address_line1', $user->address ?? '') }}" required>
                                    <label for="address_line1">Alamat Baris 1*</label>
                                    <div class="invalid-feedback">Alamat harus diisi.</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating mb-4">
                                    <input id="address_line2" type="text" name="address_line2" class="form-control @error('address_line2') is-invalid @enderror" placeholder="Alamat Baris 2 (Opsional)" value="{{ old('address_line2', '') }}">
                                    <label for="address_line2">Alamat Baris 2 (Opsional)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
                                    <input id="province" type="text" name="province" class="form-control @error('province') is-invalid @enderror" placeholder="Provinsi" value="{{ old('province', $user->province->name ?? '') }}" required>
                                    <label for="province">Provinsi*</label>
                                    <div class="invalid-feedback">Provinsi harus diisi.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
                                    <input id="city" type="text" name="city" class="form-control @error('city') is-invalid @enderror" placeholder="Kota" value="{{ old('city', $user->city->name ?? '') }}" required>
                                    <label for="city">Kota*</label>
                                    <div class="invalid-feedback">Kota harus diisi.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-4">
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
                                    <p class="price" id="order-subtotal">Rp. {{ number_format($cartSubtotal ?? 0, 0, ',', '.') }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-0"><strong class="text-dark">Pengiriman</strong></td>
                                <td class="pe-0 text-end">
                                    <p class="price" id="order-shipping">Rp. {{ number_format(old('shipping_method') == 'Pengiriman Ekspres' ? 10000 : 0, 0, ',', '.') }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-0"><strong class="text-dark">Total Akhir</strong></td>
                                <td class="pe-0 text-end">
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
{{-- Load Midtrans Snap JS --}}
<script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkoutForm = document.getElementById('checkout-form');
    const placeOrderButton = document.getElementById('place-order-button');
    const loadingIndicator = document.getElementById('loading-indicator');
    const btnText = placeOrderButton.querySelector('.btn-text');
    const btnLoading = placeOrderButton.querySelector('.btn-loading');

    // Get CSRF token from Laravel
    const csrfToken = document.querySelector('input[name="_token"]').value;

    // --- Fungsi Helper ---
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

    function hideLoading() {
        placeOrderButton.disabled = false;
        btnText.classList.remove('d-none');
        btnLoading.classList.add('d-none');
        loadingIndicator.style.display = 'none';
    }

    function showError(message) {
        const alertContainer = document.getElementById('alert-container');
        // Clear existing alerts to prevent duplicates
        alertContainer.innerHTML = '';

        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show alert-checkout-error';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        alertContainer.appendChild(alertDiv);
        alertContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function displayValidationErrors(errors) {
        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback.d-block').forEach(el => {
            el.remove();
        });

        let firstErrorField = null;

        for (const fieldName in errors) {
            let inputField = document.getElementById(fieldName) || document.querySelector(`[name="${fieldName}"]`);

            if (inputField) {
                inputField.classList.add('is-invalid');
                const errorMessage = Array.isArray(errors[fieldName]) ? errors[fieldName].join('<br>') : errors[fieldName];
                const feedbackDiv = document.createElement('div');
                feedbackDiv.className = 'invalid-feedback d-block';
                feedbackDiv.innerHTML = errorMessage;

                const existingFeedback = inputField.parentNode.querySelector('.invalid-feedback.d-block');
                if (existingFeedback) {
                    existingFeedback.remove();
                }

                inputField.parentNode.appendChild(feedbackDiv);

                if (!firstErrorField) {
                    firstErrorField = inputField;
                }
            }
        }

        if (firstErrorField) {
            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstErrorField.focus();
        }

        showError('Terjadi kesalahan validasi. Mohon perbaiki input Anda.');
    }

    // --- Event Listener untuk Form Submission ---
    checkoutForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Clear previous errors and alerts
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback.d-block').forEach(el => el.remove());
        document.getElementById('alert-container').innerHTML = ''; // Clear all alerts

        const backendErrors = document.querySelector('.backend-validation-errors');
        if (backendErrors) {
            backendErrors.style.display = 'none';
        }

        // HTML5 validation check (client-side)
        if (!checkoutForm.checkValidity()) {
            e.stopPropagation(); // Stop default form submission
            checkoutForm.classList.add('was-validated'); // Show Bootstrap validation feedback
            const firstInvalidField = checkoutForm.querySelector(':invalid');
            if (firstInvalidField) {
                firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalidField.focus();
            }
            showError('Mohon lengkapi semua field yang wajib diisi.');
            return; // Stop execution if client-side validation fails
        }

        showLoading();

        const formData = new FormData(checkoutForm);

        // Gunakan URL yang benar (pastikan ini sesuai dengan route Laravel Anda)
        const processUrl = '/checkout/process'; // atau gunakan route helper Laravel

        fetch(processUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(async response => {
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
                const data = await response.json();
                if (!response.ok) {
                    throw { type: 'api', data: data, status: response.status };
                }
                return data;
            } else {
                const text = await response.text();
                throw { type: 'server', message: `Server error: ${response.status} ${response.statusText}`, details: text };
            }
        })
        .then(data => {
            hideLoading();
            console.log('AJAX Success:', data);

            if (data.success && data.snap_token) {
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        console.log('Payment Success:', result);
                        window.location.href = "/checkout/success?order_id=" + data.order_id;
                    },
                    onPending: function(result) {
                        console.log('Payment Pending:', result);
                        window.location.href = "/checkout/pending?order_id=" + data.order_id;
                    },
                    onError: function(result) {
                        console.log('Payment Error:', result);
                        showError('Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
                    },
                    onClose: function() {
                        console.log('Payment popup closed by user');
                        showError('Pembayaran dibatalkan. Status pesanan Anda: Menunggu Pembayaran.');

                        // Update status via AJAX
                        fetch('/checkout/popup-closed', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                order_id: data.order_id
                            })
                        })
                        .then(response => response.json())
                        .then(updateData => {
                            console.log('Status updated:', updateData);
                            if (!updateData.success) {
                                console.error('Failed to update status');
                            }
                        })
                        .catch(error => {
                            console.error('Error updating status:', error);
                        });
                    }
                });
            } else if (data.errors) {
                displayValidationErrors(data.errors);
            } else {
                showError(data.message || 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('AJAX Error:', error);

            if (error.type === 'api') {
                // Handle API errors (non-2xx responses with JSON)
                if (error.data.errors) {
                    displayValidationErrors(error.data.errors);
                } else {
                    showError(error.data.message || `Terjadi kesalahan (${error.status}). Silakan coba lagi.`);
                }
            } else if (error.type === 'server') {
                // Handle server errors (non-JSON responses)
                showError(`Terjadi kesalahan server. Silakan coba lagi atau hubungi admin.`);
                console.error('Server Error Details:', error.details);
            } else {
                // Handle network errors or other exceptions
                showError('Terjadi kesalahan jaringan. Silakan cek koneksi Anda dan coba lagi.');
            }
        });
    });

    // --- Handling Shipping Method Change ---
    const shippingRadios = document.querySelectorAll('input[name="shipping_method"]');
    const orderSubtotalElement = document.getElementById('order-subtotal');
    const orderShippingElement = document.getElementById('order-shipping');
    const orderGrandTotalElement = document.getElementById('order-grand-total');

    function updateOrderSummary() {
        let selectedShippingCost = 0;
        shippingRadios.forEach(radio => {
            if (radio.checked) {
                selectedShippingCost = parseFloat(radio.dataset.cost) || 0;
            }
        });

        const subtotalText = orderSubtotalElement.textContent.replace('Rp. ', '').replace(/\./g, '');
        const currentSubtotal = parseFloat(subtotalText) || 0;

        const newGrandTotal = currentSubtotal + selectedShippingCost;

        orderShippingElement.textContent = 'Rp. ' + new Intl.NumberFormat('id-ID').format(selectedShippingCost);
        orderGrandTotalElement.textContent = 'Rp. ' + new Intl.NumberFormat('id-ID').format(newGrandTotal);
    }

    shippingRadios.forEach(radio => {
        radio.addEventListener('change', updateOrderSummary);
    });

    // Initialize order summary on page load
    updateOrderSummary();
});
</script>
@endpush
