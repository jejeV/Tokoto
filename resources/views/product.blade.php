@extends('layouts.app')

@section('title', $product->name . ' Detail')

@section('content')
<section class="wrapper bg-gray">
    <div class="container py-3 py-md-5">
        <nav class="d-inline-block" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('collections') }}">Shop</a></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">{{ $product->category ?? 'Products' }}
                </li>
            </ol>
        </nav>
    </div>
</section>

<section class="wrapper bg-light">
    <div class="container py-14 py-md-16">
        @if(session('success_add_to_cart'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success_add_to_cart')['message'] ?? 'Produk berhasil ditambahkan!' }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error_add_to_cart'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error_add_to_cart')['message'] ?? 'Gagal menambahkan produk!' }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div class="row gx-md-8 gx-xl-12 gy-8">
            <div class="col-lg-6">
                <div class="swiper-container swiper-thumbs-container" data-margin="10" data-dots="false" data-nav="true"
                    data-thumbs="true">
                    <div class="swiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <figure class="rounded">
                                    <img src="{{ asset('assets/home/img/photos/' . $product->image) }}"
                                        srcset="{{ asset('assets/home/img/photos/' . $product->image) }} 2x"
                                        alt="{{ $product->name }}" />
                                    <a class="item-link" href="{{ asset('assets/home/img/photos/' . $product->image) }}"
                                        data-glightbox data-gallery="product-group"><i
                                            class="uil uil-focus-add"></i></a>
                                </figure>
                            </div>
                    </div>
                </div>
                <div class="swiper swiper-thumbs">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide"><img src="{{ asset('assets/home/img/photos/' . $product->image) }}"
                                srcset="{{ asset('assets/home/img/photos/' . $product->image) }} 2x" class="rounded"
                                alt="{{ $product->name }}" /></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="post-header mb-5">
            <h2 class="post-title display-5">{{ $product->name }}</h2>
            <p class="price fs-20 mb-2"><span class="amount">${{ number_format($product->price, 2) }}</span></p>
            <a href="#" class="link-body ratings-wrapper"><span class="ratings five"></span><span>(0 Reviews)</span></a>
            {{-- Rating masih statis --}}
        </div>
        <p class="mb-6">{{ $product->description }}</p>


        <form action="{{ route('cart.add') }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <fieldset class="picker">
                <legend class="h6 fs-16 text-body mb-3">Choose a size</legend>
                <label for="size-xs">
                    <input type="radio" name="size" id="size-xs" value="XS" checked>
                    <span>XS</span>
                </label>
                <label for="size-s">
                    <input type="radio" name="size" id="size-s" value="S">
                    <span>S</span>
                </label>
            </fieldset>
            <fieldset class="picker">
                <legend class="h6 fs-16 text-body mb-3">Choose a color</legend>
                <label for="color-1" style="--color:#fab758">
                    <input type="radio" name="color" id="color-1" value="Yellow" checked>
                    <span>Yellow</span>
                </label>
                <label for="color-2" style="--color:#e2626b">
                    <input type="radio" name="color" id="color-2" value="Red">
                    <span>Red</span>
                </label>
            </fieldset>
            <div class="row">
                <div class="col-lg-9 d-flex flex-row pt-2">
                    <div>
                        <div class="form-select-wrapper">
                            <select class="form-select" name="quantity">
                                @php
                                $maxQuantity = ($product->stock > 0) ? ($product->stock > 5 ? 5 : $product->stock) : 0;
                                @endphp

                                @if($maxQuantity === 0)
                                <option value="0" disabled selected>Stok Habis</option>
                                @else
                                @for ($i = 1; $i <= $maxQuantity; $i++) <option value="{{ $i }}" @if($i==1) selected
                                    @endif>{{ $i }}</option>
                                    @endfor
                                    @endif
                            </select>
                        </div>
                    </div>
                    <div class="flex-grow-1 mx-2">
                        @guest
                        <button type="button" class="btn btn-primary btn-icon btn-icon-start rounded w-100 flex-grow-1"
                            onclick="window.location='{{ route('login') }}';">
                            <i class="uil uil-signin"></i> Login to Add
                        </button>
                        @else
                        @if (($product->stock ?? 0) > 0)
                        <button type="submit" class="btn btn-primary btn-icon btn-icon-start rounded w-100 flex-grow-1">
                            <i class="uil uil-shopping-bag"></i> Add to Cart
                        </button>
                        @else
                        <button type="button"
                            class="btn btn-secondary btn-icon btn-icon-start rounded w-100 flex-grow-1" disabled>
                            <i class="uil uil-ban"></i> Sold Out
                        </button>
                        @endif
                        @endguest
                    </div>
                    <div>
                        <button class="btn btn-block btn-red btn-icon rounded px-3 w-100 h-100"><i
                                class="uil uil-heart"></i></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    </div>
    <ul class="nav nav-tabs nav-tabs-basic mt-12">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#tab1-1">Product Details</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab1-2">Additional Info</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab1-3">Delivery</a>
        </li>
    </ul>
    <div class="tab-content mt-0 mt-md-5">
        <div class="tab-pane fade show active" id="tab1-1">
            <p>{{ $product->description }}</p>
        </div>
        <div class="tab-pane fade" id="tab1-2">
            <p>Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Vivamus sagittis lacus vel augue
                laoreet rutrum faucibus dolor auctor. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.
                Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Lorem ipsum dolor sit amet,
                consectetur adipiscing elit.</p>
        </div>
        <div class="tab-pane fade" id="tab1-3">
            <p>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit
                amet risus. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Cras mattis consectetur
                purus sit amet fermentum. Maecenas sed diam eget risus varius blandit sit amet non magna. Sed posuere
                consectetur est at lobortis. Curabitur blandit tempus porttitor. Aenean lacinia bibendum nulla sed
                consectetur. Nulla vitae elit libero, a pharetra augue. Morbi leo risus, porta ac consectetur ac,
                vestibulum at eros. Praesent commodo cursus magna, vel scelerisque nisl consectetur et.</p>
        </div>
    </div>
    </div>
</section>
@endsection
