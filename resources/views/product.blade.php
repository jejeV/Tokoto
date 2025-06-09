@extends('layouts.app')

@section('title', $product->name . ' Detail')

@section('content')
<section class="wrapper bg-gray">
    <div class="container py-3 py-md-5">
        <nav class="d-inline-block" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('collections') }}">Shop</a></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>
    </div>
</section>

<section class="wrapper bg-light">
    <div class="container py-14 py-md-16">
        {{-- Alert messages dari session --}}
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
                            {{-- Main Product Image --}}
                            <div class="swiper-slide">
                                <figure class="rounded">
                                    <img id="main-product-image"
                                        src="{{ asset('assets/home/img/photos/' . ($defaultVariant->image ?? $product->image)) }}"
                                        srcset="{{ asset('assets/home/img/photos/' . ($defaultVariant->image ?? $product->image)) }} 2x"
                                        alt="{{ $product->name }}" />
                                    <a class="item-link"
                                        href="{{ asset('assets/home/img/photos/' . ($defaultVariant->image ?? $product->image)) }}"
                                        data-glightbox data-gallery="product-group"><i
                                            class="uil uil-focus-add"></i></a>
                                </figure>
                            </div>
                            @foreach($product->productVariants as $variant)
                                @if($variant->image && $variant->image != ($defaultVariant->image ?? $product->image))
                                <div class="swiper-slide">
                                    <figure class="rounded">
                                        <img src="{{ asset('assets/home/img/photos/' . $variant->image) }}"
                                            srcset="{{ asset('assets/home/img/photos/' . $variant->image) }} 2x"
                                            alt="{{ $product->name }} - {{ $variant->color->name ?? '' }} {{ $variant->size->name ?? '' }}" />
                                        <a class="item-link"
                                            href="{{ asset('assets/home/img/photos/' . $variant->image) }}"
                                            data-glightbox data-gallery="product-group"><i
                                                class="uil uil-focus-add"></i></a>
                                    </figure>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="swiper swiper-thumbs">
                        <div class="swiper-wrapper">
                            {{-- Thumbnails --}}
                            <div class="swiper-slide">
                                <img src="{{ asset('assets/home/img/photos/' . ($defaultVariant->image ?? $product->image)) }}"
                                    srcset="{{ asset('assets/home/img/photos/' . ($defaultVariant->image ?? $product->image)) }} 2x"
                                    class="rounded" alt="{{ $product->name }}" />
                            </div>
                            @foreach($product->productVariants as $variant)
                                @if($variant->image && $variant->image != ($defaultVariant->image ?? $product->image))
                                <div class="swiper-slide">
                                    <img src="{{ asset('assets/home/img/photos/' . $variant->image) }}"
                                        srcset="{{ asset('assets/home/img/photos/' . $variant->image) }} 2x"
                                        class="rounded"
                                        alt="{{ $product->name }} - {{ $variant->color->name ?? '' }} {{ $variant->size->name ?? '' }}" />
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="post-header mb-5">
                    <h2 class="post-title display-5">{{ $product->name }}</h2>
                    <p class="price fs-20 mb-2"><span class="amount">
                        Rp{{ number_format($defaultVariant->price ?? $product->price, 0, ',', '.') }}</span>
                    </p>
                    <a href="#" class="link-body ratings-wrapper"><span class="ratings five"></span><span>(0
                            Reviews)</span></a>
                </div>
                <p class="mb-6">{!! $product->description !!}</p>

                <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    @if(!empty($variantsBySize) && count($variantsBySize) > 0)
                    <fieldset class="picker mb-4">
                        <legend class="h6 fs-16 text-body mb-3">Choose a size</legend>
                        <div class="size-options d-flex flex-wrap gap-2">
                            @foreach($variantsBySize as $sizeName => $variants)
                                @php
                                    $sizeId = $variants->first()->size->id ?? '';
                                    $isChecked = ($defaultVariant && ($defaultVariant->size_id == $sizeId || ($defaultVariant->size_id === null && $sizeId === '')) ) ? 'checked' : '';
                                @endphp
                            <label for="size-{{ $sizeId }}">
                                <input type="radio" name="selected_size_id" id="size-{{ $sizeId }}"
                                    value="{{ $sizeId }}" {{ $isChecked }} required>
                                <span>{{ $sizeName }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('selected_size_id')
                        <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </fieldset>
                    @endif

                    @if(!empty($variantsByColor) && count($variantsByColor) > 0)
                    <fieldset class="picker mb-4">
                        <legend class="h6 fs-16 text-body mb-3">Choose a color</legend>
                        <div class="color-options d-flex flex-wrap gap-2">
                            @foreach($variantsByColor as $colorName => $variants)
                                @php
                                    $colorId = $variants->first()->color->id ?? '';
                                    $colorCode = $allColors->firstWhere('id', $colorId)->hex_code ?? '#ccc';
                                    $isChecked = ($defaultVariant && ($defaultVariant->color_id == $colorId || ($defaultVariant->color_id === null && $colorId === '')) ) ? 'checked' : '';
                                @endphp
                            <label for="color-{{ $colorId }}" style="--color:{{ $colorCode }}">
                                <input type="radio" name="selected_color_id" id="color-{{ $colorId }}"
                                    value="{{ $colorId }}" {{ $isChecked }} required>
                                <span>{{ $colorName }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('selected_color_id')
                        <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </fieldset>
                    @endif

                    <div class="row">
                        <div class="col-lg-9 d-flex flex-row pt-2">
                            <div>
                                <div class="form-select-wrapper">
                                    <select class="form-select" name="quantity" id="quantity_select">
                                        @php
                                            $initialMaxQuantity = ($defaultVariant && $defaultVariant->stock > 0)
                                                ? min($defaultVariant->stock, 5)
                                                : (($product->stock ?? 0) > 0 ? min(($product->stock ?? 0), 5) : 0);
                                        @endphp
                                        @if ($initialMaxQuantity === 0)
                                        <option value="0" disabled selected>Stok Habis</option>
                                        @else
                                        @for ($i = 1; $i <= $initialMaxQuantity; $i++)
                                            <option value="{{ $i }}" @if($i==1) selected @endif>{{ $i }}</option>
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
                                <button type="submit" id="addToCartButton"
                                    class="btn btn-primary btn-icon btn-icon-start rounded w-100 flex-grow-1"
                                    {{ ($initialMaxQuantity === 0) ? 'disabled' : '' }}>
                                    <i class="uil uil-shopping-bag"></i> Add to Cart
                                </button>
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
                <p>{!! $product->description !!}</p>
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
