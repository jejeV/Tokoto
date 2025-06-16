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
        {{-- Notifikasi Alert dari session flash (Untuk submit form non-AJAX) --}}
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
        {{-- End Notifikasi Alert --}}


        <div class="row gx-md-8 gx-xl-12 gy-8">
            <div class="col-lg-6">
                <div class="swiper-container swiper-thumbs-container" data-margin="10" data-dots="false" data-nav="true"
                    data-thumbs="true">
                    <div class="swiper">
                        <div class="swiper-wrapper">
                            {{-- Main Product Image (akan berubah via JS) --}}
                            <div class="swiper-slide">
                                <figure class="rounded">
                                    <img id="main-product-image"
                                        src="{{ asset('assets/home/img/nike/' . ($defaultVariant->image ?? $product->image)) }}"
                                        srcset="{{ asset('assets/home/img/nike/' . ($defaultVariant->image ?? $product->image)) }} 2x"
                                        alt="{{ $product->name }}" />
                                    <a class="item-link"
                                        href="{{ asset('assets/home/img/photos/' . ($defaultVariant->image ?? $product->image)) }}"
                                        data-glightbox data-gallery="product-group"><i
                                            class="uil uil-focus-add"></i></a>
                                </figure>
                            </div>
                            {{-- Additional Variant Images (if any, make sure they are unique) --}}
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
                    <p class="price fs-20 mb-2"><span class="amount" id="product-price">
                        Rp{{ number_format($defaultVariant->price ?? $product->price, 0, ',', '.') }}</span>
                    </p>
                    <a href="#" class="link-body ratings-wrapper"><span class="ratings five"></span><span>(0
                                Reviews)</span></a>
                </div>
                <p class="mb-6">{!! $product->description !!}</p>

                <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    {{-- product_variant_id akan diperbarui oleh JavaScript --}}
                    <input type="hidden" name="product_variant_id" id="selected-product-variant-id" value="{{ $defaultVariant->id ?? '' }}">

                    {{-- Hidden inputs for selected size and color IDs --}}
                    <input type="hidden" name="selected_size_id" id="hidden-selected-size-id" value="{{ $defaultVariant->size_id ?? '' }}">
                    <input type="hidden" name="selected_color_id" id="hidden-selected-color-id" value="{{ $defaultVariant->color_id ?? '' }}">

                    {{-- Choose a size section --}}
                    @if(!$allSizes->isEmpty())
                    <fieldset class="picker mb-4">
                        <legend class="h6 fs-16 text-body mb-3">Choose a size</legend>
                        <div class="size-options d-flex flex-wrap gap-2">
                            @foreach($allSizes as $size)
                                <button type="button"
                                        class="btn btn-outline-primary btn-sm rounded-pill size-option"
                                        data-size-id="{{ $size->id }}"
                                        data-size-name="{{ $size->name }}"
                                        {{-- disabled status akan dikelola oleh JS --}}>
                                    {{ $size->name }}
                                </button>
                            @endforeach
                        </div>
                    </fieldset>
                    @endif

                    {{-- Choose a color section --}}
                    @if(!$allColors->isEmpty())
                    <fieldset class="picker mb-4">
                        <legend class="h6 fs-16 text-body mb-3">Choose a color</legend>
                        <div class="color-options d-flex flex-wrap gap-2">
                            @foreach($allColors as $color)
                                {{-- Updated color button styling for better visual --}}
                                <button type="button"
                                        class="btn btn-sm rounded-circle color-option"
                                        style="background-color: {{ $color->hex_code ?? '#CCCCCC' }}; border: 2px solid #ccc; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;"
                                        data-color-id="{{ $color->id }}"
                                        data-color-name="{{ $color->name }}"
                                        {{-- disabled status akan dikelola oleh JS --}}>
                                    <i class="uil uil-check-circle text-white check-icon" style="font-size: 1.5rem; display: none;"></i>
                                </button>
                            @endforeach
                        </div>
                    </fieldset>
                    @endif

                    <div class="row">
                        <div class="col-lg-9 d-flex flex-row pt-2">
                            <div>
                                <div class="form-select-wrapper">
                                    <select class="form-select" name="quantity" id="quantity_select">
                                        {{-- Opsi kuantitas akan diisi oleh JS berdasarkan stok yang tersedia --}}
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
                                        class="btn btn-primary btn-icon btn-icon-start rounded w-100 flex-grow-1">
                                        <i class="uil uil-shopping-bag"></i> Add to Cart
                                    </button>
                                @endguest
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

        <script>
            const variantMap = @json($variantMap);
            const allSizes = @json($allSizes->pluck('id')->toArray());
            const allColors = @json($allColors->pluck('id')->toArray());
            const defaultVariantData = @json($defaultVariant);
            const productId = {{ $product->id }};

            let selectedSizeId = defaultVariantData.size_id;
            let selectedColorId = defaultVariantData.color_id;

            const productMainImage = document.getElementById('main-product-image');
            const productPrice = document.getElementById('product-price');
            const quantitySelect = document.getElementById('quantity_select');
            const selectedProductVariantIdInput = document.getElementById('selected-product-variant-id');
            const hiddenSelectedSizeIdInput = document.getElementById('hidden-selected-size-id');
            const hiddenSelectedColorIdInput = document.getElementById('hidden-selected-color-id');
            const addToCartButton = document.getElementById('addToCartButton');

            function updateVariantUI() {
                let currentVariant = null;

                const sizeKey = selectedSizeId === null ? 'null_size' : selectedSizeId;
                const colorKey = selectedColorId === null ? 'null_color' : selectedColorId;

                if (variantMap[sizeKey] && variantMap[sizeKey][colorKey]) {
                    currentVariant = variantMap[sizeKey][colorKey];
                } else if (allSizes.length === 0 && allColors.length === 0) {
                    currentVariant = defaultVariantData;
                }

                // Update UI (harga, gambar, stok, ID varian)
                if (currentVariant) {
                    productPrice.textContent = `Rp. ${currentVariant.price.toLocaleString('id-ID')}`;
                    if (currentVariant.image) {
                        productMainImage.src = `{{ asset('assets/home/img/nike/') }}/${currentVariant.image}`;
                        productMainImage.srcset = `{{ asset('assets/home/img/nike/') }}/${currentVariant.image} 2x`;
                    } else if (defaultVariantData.image) {
                        productMainImage.src = `{{ asset('assets/home/img/nike/') }}/${defaultVariantData.image}`;
                        productMainImage.srcset = `{{ asset('assets/home/img/nike/') }}/${defaultVariantData.image} 2x`;
                    }
                    selectedProductVariantIdInput.value = currentVariant.id || '';
                    hiddenSelectedSizeIdInput.value = selectedSizeId || '';
                    hiddenSelectedColorIdInput.value = selectedColorId || '';
                    updateQuantityOptions(currentVariant.stock);
                } else {
                    productPrice.textContent = 'Rp. {{ number_format($product->price, 0, ',', '.') }}';
                    productMainImage.src = `{{ asset('assets/home/img/photos/') }}/{{ $product->image }}`;
                    productMainImage.srcset = `{{ asset('assets/home/img/photos/') }}/{{ $product->image }} 2x`;
                    selectedProductVariantIdInput.value = '';
                    hiddenSelectedSizeIdInput.value = selectedSizeId || '';
                    hiddenSelectedColorIdInput.value = selectedColorId || '';
                    updateQuantityOptions(0);
                }

                updateButtonStates();
            }

            // Fungsi untuk memperbarui opsi kuantitas berdasarkan stok
            function updateQuantityOptions(stock) {
                quantitySelect.innerHTML = '';
                const maxQuantityToShow = Math.min(stock, 10);

                if (maxQuantityToShow === 0) {
                    const option = document.createElement('option');
                    option.value = 0;
                    option.textContent = 'Stok Habis';
                    option.disabled = true;
                    option.selected = true;
                    quantitySelect.appendChild(option);
                    addToCartButton.disabled = true;
                    addToCartButton.innerHTML = '<i class="uil uil-shopping-bag"></i> Stok Habis';
                } else {
                    for (let i = 1; i <= maxQuantityToShow; i++) {
                        const option = document.createElement('option');
                        option.value = i;
                        option.textContent = i;
                        if (i === 1) option.selected = true;
                        quantitySelect.appendChild(option);
                    }
                    addToCartButton.disabled = false;
                    addToCartButton.innerHTML = '<i class="uil uil-shopping-bag"></i> Add to Cart';
                }
            }

            // Fungsi untuk memperbarui status disable/active pada tombol ukuran dan warna
            function updateButtonStates() {
                // Update size buttons
                document.querySelectorAll('.size-option').forEach(button => {
                    const sizeId = button.dataset.sizeId === '' ? null : parseInt(button.dataset.sizeId);
                    const sizeKey = sizeId === null ? 'null_size' : sizeId;

                    let isAnyColorAvailableForThisSize = false;
                    if (allColors.length > 0) {
                        for (const colorId of allColors) {
                            const colorKey = colorId === null ? 'null_color' : colorId;
                            if (variantMap[sizeKey] && variantMap[sizeKey][colorKey] && variantMap[sizeKey][colorKey].stock > 0) {
                                isAnyColorAvailableForThisSize = true;
                                break;
                            }
                        }
                    } else { // No color options, check against 'null_color'
                        if (variantMap[sizeKey] && variantMap[sizeKey]['null_color'] && variantMap[sizeKey]['null_color'].stock > 0) {
                            isAnyColorAvailableForThisSize = true;
                        }
                    }

                    button.disabled = !isAnyColorAvailableForThisSize;
                    button.classList.toggle('active', sizeId === selectedSizeId);
                });

                // Update color buttons
                document.querySelectorAll('.color-option').forEach(button => {
                    const colorId = button.dataset.colorId === '' ? null : parseInt(button.dataset.colorId);
                    const colorKey = colorId === null ? 'null_color' : colorId;

                    let isAnySizeAvailableForThisColor = false;
                    if (allSizes.length > 0) {
                        for (const sizeId of allSizes) {
                            const sizeKey = sizeId === null ? 'null_size' : sizeId;
                            if (variantMap[sizeKey] && variantMap[sizeKey][colorKey] && variantMap[sizeKey][colorKey].stock > 0) {
                                isAnySizeAvailableForThisColor = true;
                                break;
                            }
                        }
                    } else { // No size options, check against 'null_size'
                        if (variantMap['null_size'] && variantMap['null_size'][colorKey] && variantMap['null_size'][colorKey].stock > 0) {
                            isAnySizeAvailableForThisColor = true;
                        }
                    }
                    button.disabled = !isAnySizeAvailableForThisColor;
                    button.classList.toggle('active', colorId === selectedColorId);

                    // Show/hide checkmark icon based on active state
                    const checkIcon = button.querySelector('.check-icon');
                    if (checkIcon) {
                        checkIcon.style.display = button.classList.contains('active') ? 'block' : 'none';
                    }

                    // Adjust border for active state
                    if (button.classList.contains('active')) {
                        button.style.border = '2px solid var(--bs-primary)'; // Highlight with primary color border
                    } else {
                        button.style.border = '2px solid #ccc'; // Default light border
                    }

                    // Adjust opacity for disabled state
                    if (button.disabled) {
                        button.style.opacity = '0.5';
                        button.style.cursor = 'not-allowed';
                    } else {
                        button.style.opacity = '1';
                        button.style.cursor = 'pointer';
                    }
                });

                // Pastikan tombol Add to Cart mencerminkan stok varian yang sedang dipilih
                let currentSelectedVariantStock = 0;
                const finalSizeKey = selectedSizeId === null ? 'null_size' : selectedSizeId;
                const finalColorKey = selectedColorId === null ? 'null_color' : selectedColorId;

                if (variantMap[finalSizeKey] && variantMap[finalSizeKey][finalColorKey]) {
                    currentSelectedVariantStock = variantMap[finalSizeKey][finalColorKey].stock;
                } else if (allSizes.length === 0 && allColors.length === 0) {
                    currentSelectedVariantStock = defaultVariantData.stock;
                }

                if (addToCartButton) {
                    if (currentSelectedVariantStock === 0 || currentSelectedVariantStock === undefined) {
                        addToCartButton.disabled = true;
                        addToCartButton.innerHTML = '<i class="uil uil-shopping-bag"></i> Stok Habis';
                    } else {
                        addToCartButton.disabled = false;
                        addToCartButton.innerHTML = '<i class="uil uil-shopping-bag"></i> Add to Cart';
                    }
                }
            }


            // Event Listeners for Size and Color buttons
            document.querySelectorAll('.size-option').forEach(button => {
                button.addEventListener('click', function() {
                    if (this.disabled) return; // Prevent action if disabled

                    document.querySelectorAll('.size-option').forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    selectedSizeId = this.dataset.sizeId === '' ? null : parseInt(this.dataset.sizeId);
                    updateVariantUI();
                });
            });

            document.querySelectorAll('.color-option').forEach(button => {
                button.addEventListener('click', function() {
                    if (this.disabled) return; // Prevent action if disabled

                    document.querySelectorAll('.color-option').forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    selectedColorId = this.dataset.colorId === '' ? null : parseInt(this.dataset.colorId);
                    updateVariantUI();
                });
            });

            document.addEventListener('DOMContentLoaded', () => {
                let initialSizeId = defaultVariantData.size_id;
                let initialColorId = defaultVariantData.color_id;

                if (initialSizeId === null && allSizes.length > 0) {
                    const firstAvailableSizeBtn = document.querySelector('.size-option:not([disabled])');
                    if (firstAvailableSizeBtn) {
                        initialSizeId = parseInt(firstAvailableSizeBtn.dataset.sizeId);
                    }
                }
                if (initialColorId === null && allColors.length > 0) {
                    const firstAvailableColorBtn = document.querySelector('.color-option:not([disabled])');
                    if (firstAvailableColorBtn) {
                        initialColorId = parseInt(firstAvailableColorBtn.dataset.colorId);
                    }
                }

                selectedSizeId = initialSizeId;
                selectedColorId = initialColorId;

                if (selectedSizeId !== null) {
                    const defaultSizeBtn = document.querySelector(`.size-option[data-size-id="${selectedSizeId}"]`);
                    if (defaultSizeBtn) defaultSizeBtn.classList.add('active');
                } else if (allSizes.length === 0) {
                    selectedSizeId = null;
                }


                if (selectedColorId !== null) {
                    const defaultColorBtn = document.querySelector(`.color-option[data-color-id="${selectedColorId}"]`);
                    if (defaultColorBtn) defaultColorBtn.classList.add('active');
                } else if (allColors.length === 0) {
                    selectedColorId = null;
                }
                updateVariantUI();
            });

        </script>

    </div>
</section>
@endsection
