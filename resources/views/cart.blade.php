@extends('layouts.app')

@section('title', 'Your Shopping Cart')

@section('content')

<section class="wrapper bg-light">
    <div class="container pt-12 pt-md-14 pb-14 pb-md-16">
        <div class="row gx-md-8 gx-xl-12 gy-12">
            <div class="col-lg-8">
                <h1 class="display-5 text-center mb-6">Your Shopping Cart</h1>
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

                @if(empty($cartItems))
                    <div class="text-center">
                        <p class="lead">Your cart is empty. <a href="{{ route('collections') }}">Start shopping!</a></p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table text-center shopping-cart">
                            <thead>
                                <tr>
                                    <th class="ps-0 w-25">
                                        <div class="h4 mb-0 text-start">Product</div>
                                    </th>
                                    <th>
                                        <div class="h4 mb-0">Price</div>
                                    </th>
                                    <th>
                                        <div class="h4 mb-0">Quantity</div>
                                    </th>
                                    <th>
                                        <div class="h4 mb-0">Total</div>
                                    </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartItems as $item)
                                    <tr>
                                        <td class="option text-start d-flex flex-row align-items-center ps-0">
                                            <figure class="rounded w-17">
                                                {{-- Mengakses properti item di dalam array --}}
                                                {{-- Pastikan atribut 'image' ada di item keranjang, perhatikan struktur data $item --}}
                                                @if(isset($item['image']))
                                                    <a href="{{ route('shop.product.detail', $item['id']) }}">
                                                        <img src="{{ asset('assets/home/img/photos/' . $item['image']) }}" alt="{{ $item['name'] }}" />
                                                    </a>
                                                @else
                                                    {{-- Placeholder jika gambar tidak tersedia --}}
                                                    <a href="{{ route('shop.product.detail', $item['id']) }}">
                                                        <img src="{{ asset('assets/img/photos/default-product.jpg') }}" alt="No Image" />
                                                    </a>
                                                @endif
                                            </figure>
                                            <div class="w-100 ms-4">
                                                <h3 class="post-title h6 lh-xs mb-1">
                                                    <a href="{{ route('shop.product.detail', $item['id']) }}" class="link-dark">{{ $item['name'] }}</a>
                                                </h3>
                                                {{-- Jika kamu menyimpan atribut lain seperti warna/ukuran, tampilkan di sini --}}
                                                {{-- <div class="small">Color: {{ $item['attributes']['color'] ?? '' }}</div> --}}
                                                {{-- <div class="small">Size: {{ $item['attributes']['size'] ?? '' }}</div> --}}
                                            </div>
                                        </td>
                                        <td>
                                            <p class="price">
                                                <span class="amount">${{ number_format($item['price'], 2) }}</span>
                                            </p>
                                        </td>
                                        <td>
                                            <div class="form-select-wrapper">
                                                <form action="{{ route('cart.update') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $item['id'] }}">
                                                    <select class="form-select form-select-sm mx-auto" name="quantity" onchange="this.form.submit()" style="width: 80px;">
                                                        {{-- Batasi kuantitas hingga stok atau max 10, pastikan 'stock' ada di item keranjang --}}
                                                        @for($i = 1; $i <= ($item['stock'] ?? 10); $i++)
                                                            <option value="{{ $i }}" {{ $item['quantity'] == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </form>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="price"><span class="amount">${{ number_format($item['price'] * $item['quantity'], 2) }}</span></p>
                                        </td>
                                        <td class="pe-0">
                                            <form action="{{ route('cart.remove') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $item['id'] }}">
                                                <button type="submit" class="link-dark border-0 bg-transparent p-0">
                                                    <i class="uil uil-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-0 gy-4">
                        <div class="col-md-8 col-lg-7">
                            <a href="{{ route('collections') }}" class="btn btn-outline-primary rounded">Continue Shopping</a>
                        </div>
                        <div class="col-md-4 col-lg-5 ms-auto ms-lg-0 text-md-end">
                            {{-- Form untuk mengosongkan keranjang --}}
                            <form action="{{ route('cart.clear') }}" method="POST" class="d-inline-block">
                                @csrf
                                <button type="submit" class="btn btn-warning rounded">Clear Cart</button>
                            </form>
                        </div>
                    </div>
                @endif {{-- Akhir dari if(empty($cartItems)) --}}
            </div>
            {{-- Ringkasan Pesanan (Order Summary) --}}
            <div class="col-lg-4">
                <h3 class="mb-4">Order Summary</h3>
                <div class="table-responsive">
                    <table class="table table-order">
                        <tbody>
                            <tr>
                                <td class="ps-0"><strong class="text-dark">Subtotal</strong></td>
                                <td class="pe-0 text-end">
                                    <p class="price">${{ number_format($cartTotal, 2) }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-0"><strong class="text-dark">Grand Total</strong></td>
                                <td class="pe-0 text-end">
                                    <p class="price text-dark fw-bold">${{ number_format($cartTotal, 2) }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('checkout') }}" class="btn btn-primary rounded w-100 mt-4">Proceed to Checkout</a>
            </div>
            </div>
        </div>
    </section>
@endsection
