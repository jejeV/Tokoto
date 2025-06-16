@extends('layouts.app')

@section('title', 'Your Shopping Cart')

@section('content')
<section class="wrapper bg-gray">
    <div class="container py-3 py-md-5">
        <nav class="d-inline-block" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('collections') }}">Shop</a></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Shopping Cart</li>
            </ol>
        </nav>
    </div>
</section>

<section class="wrapper bg-light">
    <div class="container py-14 py-md-16">
        <div class="row gx-md-8 gx-xl-12 gy-10">
            <div class="col-lg-8">
                <h2 class="display-6 mb-4">Your Shopping Cart</h2>

                <div id="alert-messages-container">
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
                </div>

                @if($cartItems->isEmpty())
                    <p class="lead text-center" id="empty-cart-message">Your cart is empty. <a href="{{ route('collections') }}">Start shopping!</a></p>
                @else
                    <div class="shopping-cart mb-7" id="cart-table-container">
                        <div class="table-responsive">
                            <table class="table text-center">
                                <thead>
                                    <tr>
                                        <th class="product-name text-start">Product</th>
                                        <th class="product-price">Price</th>
                                        <th class="product-qty">Quantity</th>
                                        <th class="product-subtotal">Total</th>
                                        <th class="product-remove"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cartItems as $item)
                                    <tr id="cart-item-{{ $item->id }}">
                                        <td class="product-item text-start">
                                            <div class="product-thumb">
                                                <img src="{{ asset('assets/home/img/nike/' . $item->product->image) }}" alt="{{ $item->product->name }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;" />
                                            </div>
                                            <div class="product-info">
                                                <h5 class="product-title">{{ $item->product->name }}</h5>
                                                @if($item->productVariant) {{-- Pastikan productVariant ada --}}
                                                <div class="product-meta">
                                                    @if($item->productVariant->size)
                                                    <span class="product-size">Size: {{ $item->productVariant->size->name }}</span>
                                                    @endif
                                                    @if($item->productVariant->color)
                                                    <span class="product-color">Color: {{ $item->productVariant->color->name }}</span>
                                                    @endif
                                                </div>
                                                @endif

                                                <div class="product-variants-edit d-flex gap-2 mt-2">
                                                    <form action="{{ route('cart.update_variant', ['cartItem' => $item->id]) }}" method="POST" class="d-inline-block">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                                                        <div class="form-select-wrapper">
                                                            <select class="form-select form-select-sm" name="size_id" onchange="this.form.submit()">
                                                                <option value="">-- Size --</option>
                                                                @foreach($availableSizes as $size) {{-- Asumsi $availableSizes adalah koleksi Size models --}}
                                                                    <option value="{{ $size->id }}" {{ ($item->productVariant && $item->productVariant->size_id == $size->id) ? 'selected' : '' }}>
                                                                        {{ $size->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </form>

                                                    {{-- Form untuk Update Warna --}}
                                                    <form action="{{ route('cart.update_variant', ['cartItem' => $item->id]) }}" method="POST" class="d-inline-block">
                                                        @csrf
                                                        @method('PUT') {{-- Gunakan PUT method --}}
                                                        <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                                                        <div class="form-select-wrapper">
                                                            <select class="form-select form-select-sm" name="color_id" onchange="this.form.submit()">
                                                                <option value="">-- Color --</option>
                                                                @foreach($availableColors as $color) {{-- Asumsi $availableColors adalah koleksi Color models --}}
                                                                    <option value="{{ $color->id }}" {{ ($item->productVariant && $item->productVariant->color_id == $color->id) ? 'selected' : '' }}>
                                                                        {{ $color->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                        {{-- Gunakan $item->price (accessor dari Cart model) --}}
                                        <td class="product-price">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="product-qty">
                                            {{-- Form untuk Update Kuantitas --}}
                                            <form action="{{ route('cart.update_quantity', ['cartItem' => $item->id]) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                @method('PUT') {{-- Gunakan PUT method --}}
                                                <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                                                <div class="form-select-wrapper d-inline-block w-auto">
                                                    <select class="form-select" name="quantity" onchange="this.form.submit()">
                                                        @php
                                                            // Pastikan $item->productVariant ada dan memiliki stock
                                                            $maxQty = ($item->productVariant && $item->productVariant->stock > 0) ? (($item->productVariant->stock > 10) ? 10 : $item->productVariant->stock) : 0;
                                                        @endphp
                                                        @if($maxQty == 0)
                                                            <option value="0" disabled selected>Sold Out</option>
                                                        @else
                                                            @for($i = 1; $i <= $maxQty; $i++)
                                                                <option value="{{ $i }}" {{ $item->quantity == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                            @endfor
                                                        @endif
                                                    </select>
                                                </div>
                                            </form>
                                        </td>
                                        {{-- Perhitungan subtotal per item --}}
                                        <td class="product-subtotal">Rp{{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                                        <td class="product-remove">
                                            {{-- Form untuk Hapus Item --}}
                                            <form action="{{ route('cart.remove', ['cartItem' => $item->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this item from your cart?');">
                                                @csrf
                                                @method('DELETE') {{-- Gunakan DELETE method --}}
                                                <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                                                <button type="submit" class="btn btn-sm btn-icon btn-outline-flicker ms-auto">
                                                    <i class="uil uil-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mb-5" id="cart-actions-row">
                        <div class="col-md-6">
                            <a href="{{ route('collections') }}" class="btn btn-outline-primary rounded-pill btn-icon btn-icon-start">
                                <i class="uil uil-shopping-bag"></i> Continue Shopping
                            </a>
                        </div>
                        <div class="col-md-6 text-end">
                            {{-- Button Clear Cart --}}
                            <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear your entire cart?');">
                                @csrf
                                @method('DELETE') {{-- Gunakan DELETE method --}}
                                <button type="submit" class="btn btn-outline-danger rounded-pill btn-icon btn-icon-start">
                                    <i class="uil uil-times-circle"></i> Clear Cart
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4" id="order-summary-col">
                <div class="shopping-cart-summary">
                    <h3 class="display-6 mb-4">Order Summary</h3>
                    <table class="table table-cart-summary">
                        <tbody>
                            <tr>
                                <td>Subtotal</td>
                                <td class="text-end" id="cart-subtotal">Rp{{ number_format($cartTotal, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Grand Total</th>
                                <th class="text-end" id="cart-grand-total">Rp{{ number_format($cartTotal, 0, ',', '.') }}</th>
                            </tr>
                        </tbody>
                    </table>
                    <a href="{{ route('checkout.show') }}" class="btn btn-primary rounded-pill w-100">Proceed to Checkout</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
