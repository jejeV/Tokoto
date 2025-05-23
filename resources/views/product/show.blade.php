@extends('layouts.app') {{-- Menggunakan layout utama --}}

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                {{-- Gambar Produk --}}
                <img src="{{ asset('img/shoes/' . $product->image) }}" class="img-fluid" alt="{{ $product->name }}">
            </div>
            <div class="col-md-6">
                {{-- Informasi Produk --}}
                <h1>{{ $product->name }}</h1>
                <p>Harga: Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                <p>Deskripsi: {{ $product->description }}</p>
                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
@endsection
