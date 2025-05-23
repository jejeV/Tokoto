@extends('layouts.app') {{-- Menggunakan layout utama --}}

@section('content')
    <div class="container py-5">
        <h1 class="mb-4">Semua Produk</h1>

        <div class="row">
            {{-- Sidebar Kategori --}}
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">Kategori</div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach ($categories as $category)
                                <li class="list-group-item">
                                    <a href="{{ route('products.index', ['category' => $category->slug]) }}">
                                        {{ $category->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Daftar Produk --}}
            <div class="col-md-9">
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    @forelse ($products as $product)
                        <div class="col">
                            <div class="card h-100">
                                <img src="{{ asset('img/shoes/' . $product->image) }}" class="card-img-top"
                                    alt="{{ $product->name }}">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $product->name }}</h5>
                                    <p class="card-text">Harga: Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-primary">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted">Tidak ada produk ditemukan.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Paginasi --}}
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
