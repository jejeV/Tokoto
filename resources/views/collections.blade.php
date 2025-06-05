@extends('layouts.app')

@section('title', 'Collections')

@section('content')
<section class="wrapper bg-light">
    <div class="container py-14 py-md-16">
        <div class="row align-items-center mb-10 position-relative zindex-1">
            <div class="col-md-8 col-lg-9 col-xl-8 col-xxl-7 pe-xl-20">
                <h2 class="display-6">New Arrivals</h2>
                <nav class="d-inline-block" aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Shop</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-4 col-lg-3 ms-md-auto text-md-end mt-5 mt-md-0">
                <div class="form-select-wrapper">
                    <select class="form-select">
                        <option value="popularity">Sort by popularity</option>
                        <option value="rating">Sort by average rating</option>
                        <option value="newness">Sort by newness</option>
                        <option value="price: low to high">Sort by price: low to high</option>
                        <option value="price: high to low">Sort by price: high to low</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="grid grid-view projects-masonry shop mb-13">
            <div class="row gx-md-8 gy-10 gy-md-13 isotope">
                @forelse ($products as $product)
                    <div class="project item col-md-6 col-xl-4">
                        <figure class="rounded mb-6">
                            <img src="{{ asset('assets/home/img/photos/' . $product->image) }}" srcset="{{ asset('assets/home/img/photos/' . $product->image) }} 2x" alt="{{ $product->name }}" />
                            <a class="item-like" href="#" data-bs-toggle="white-tooltip" title="Add to wishlist"><i class="uil uil-heart"></i></a>
                            <a class="item-view" href="#" data-bs-toggle="white-tooltip" title="Quick view"><i class="uil uil-eye"></i></a>
                            <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form" style="display:inline;">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                {{-- Input ini tidak lagi sepenuhnya diperlukan jika CartController@add mengambil data dari DB berdasarkan product_id
                                     Namun, tidak ada salahnya ada, hanya memastikan data ini konsisten dengan DB
                                <input type="hidden" name="product_name" value="{{ $product->name }}">
                                <input type="hidden" name="price" value="{{ $product->price }}">
                                <input type="hidden" name="image" value="{{ $product->image }}">
                                <input type="hidden" name="stock" value="{{ $product->stock ?? 0 }}">
                                --}}

                                @guest
                                    <button type="button" class="item-cart border-0 p-0 bg-transparent text-primary"
                                        onclick="window.location='{{ route('login') }}';"
                                        title="Anda perlu login untuk menambahkan ke keranjang">
                                        <i class="uil uil-signin"></i> Login to Add
                                    </button>
                                @else
                                    @if(Auth::check() && Auth::user()->hasRole('customer'))
                                        @if (($product->stock ?? 0) > 0)
                                            <button type="submit" class="item-cart">
                                                <i class="uil uil-shopping-bag"></i> Add to Cart
                                            </button>
                                        @else
                                            <button type="button" class="item-cart border-0 p-0 bg-transparent text-secondary" disabled>
                                                <i class="uil uil-ban"></i> Sold Out
                                            </button>
                                        @endif
                                    @else
                                        <button type="button" class="item-cart border-0 p-0 bg-transparent text-secondary" disabled
                                            title="Anda tidak memiliki izin untuk menambahkan ke keranjang.">
                                            <i class="uil uil-exclamation-circle"></i> No Permission
                                        </button>
                                    @endif
                                @endguest
                            </form>

                            @if (($product->stock ?? 0) == 0)
                                <span class="avatar bg-red text-white w-10 h-10 position-absolute text-uppercase fs-13" style="top: 1rem; left: 1rem;"><span>Sold Out!</span></span>
                            @elseif ($product->price < 50)
                                <span class="avatar bg-pink text-white w-10 h-10 position-absolute text-uppercase fs-13" style="top: 1rem; left: 1rem;"><span>Sale!</span></span>
                            @endif
                        </figure>
                        <div class="post-header">
                            <div class="d-flex flex-row align-items-center justify-content-between mb-2">
                                <div class="post-category text-ash mb-0">
                                    {{ $product->category ?? 'Category Placeholder' }}
                                </div>
                                <span class="ratings five"></span>
                            </div>
                            <h2 class="post-title h3 fs-22"><a href="{{ route('shop.product.detail', $product->id) }}" class="link-dark">{{ $product->name }}</a></h2>
                            <p class="price"><span class="amount">${{ number_format($product->price, 2) }}</span></p>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p>No products found.</p>
                    </div>
                @endforelse
            </div>
        </div>
        <nav class="d-flex justify-content-center" aria-label="pagination">
            <ul class="pagination">
                <li class="page-item disabled">
                    <a class="page-link" href="#" aria-label="Previous">
                        <span aria-hidden="true"><i class="uil uil-arrow-left"></i></span>
                    </a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#" aria-label="Next">
                        <span aria-hidden="true"><i class="uil uil-arrow-right"></i></span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</section>

<div class="modal fade" id="addToCartSuccessModal" tabindex="-1" aria-labelledby="addToCartSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h2 class="mb-3 text-start" id="modalTitle">Produk Ditambahkan!</h2>
                <p class="lead mb-3 text-start" id="modalProductName">Produk berhasil ditambahkan ke keranjang Anda.</p>
                <img id="modalProductImage" src="" alt="Product Image" style="max-width: 100px; height: auto; margin-bottom: 15px;">
                <p class="mb-3">Total item di keranjang: <span id="cartItemCount">0</span></p>
                <a href="{{ route('cart.index') }}" class="btn btn-primary rounded-pill w-100 mb-2">Lihat Keranjang</a>
                <button type="button" class="btn btn-secondary rounded-pill w-100" data-bs-dismiss="modal">Lanjutkan Belanja</button>
            </div>
        </div>
    </div>
</div>
{{-- END MODAL --}}

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const successMessage = '{{ session('success_add_to_cart.message') ?? '' }}';
        const productName = '{{ session('success_add_to_cart.product_name') ?? '' }}';
        const productImage = '{{ session('success_add_to_cart.product_image') ?? '' }}';
        const cartCount = '{{ session('success_add_to_cart.cart_count') ?? 0 }}';

        const errorMessage = '{{ session('error_add_to_cart.message') ?? '' }}';
        const errorProductName = '{{ session('error_add_to_cart.product_name') ?? '' }}';

        if (successMessage) {
            document.getElementById('modalTitle').textContent = successMessage;
            document.getElementById('modalProductName').textContent = productName + ' berhasil ditambahkan ke keranjang Anda.';

            const imgElement = document.getElementById('modalProductImage');
            if (productImage) {
                // JALUR GAMBAR DI JS JUGA DISESUAIKAN
                imgElement.src = '{{ asset('assets/home/img/photos/') }}/' + productImage;
                imgElement.style.display = 'block';
            } else {
                imgElement.style.display = 'none';
            }
            document.getElementById('cartItemCount').textContent = cartCount;

            var myModal = new bootstrap.Modal(document.getElementById('addToCartSuccessModal'));
            myModal.show();

        } else if (errorMessage) {
            document.getElementById('modalTitle').textContent = 'Gagal Menambahkan!';
            document.getElementById('modalProductName').textContent = errorProductName + ': ' + errorMessage;
            document.getElementById('modalProductImage').style.display = 'none';
            document.getElementById('cartItemCount').textContent = '{{ session('error_add_to_cart.cart_count') ?? 0 }}';

            document.querySelector('#addToCartSuccessModal .btn-primary').style.display = 'none';
            document.querySelector('#addToCartSuccessModal .btn-secondary').textContent = 'Oke';

            var myModal = new bootstrap.Modal(document.getElementById('addToCartSuccessModal'));
            myModal.show();
        }
    });
</script>
@endsection
