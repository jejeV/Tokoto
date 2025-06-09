<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Size;
use App\Models\Color;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sizes = Size::all()->pluck('id', 'name');
        $colors = Color::all()->pluck('id', 'name');

        $product1 = Product::create([
            'name' => 'Nike Air Sneakers',
            'slug' => Str::slug('Nike Air Sneakers'),
            'description' => 'Sepatu olahraga ringan dan nyaman dengan desain modern.',
            'price' => 1000000,
            'image' => 'sh1.jpg',
        ]);
        ProductVariant::create([
            'product_id' => $product1->id,
            'size_id' => $sizes['40'],
            'color_id' => $colors['Black'],
            'stock' => 5,
            'price' => 1000000,
        ]);
        ProductVariant::create([
            'product_id' => $product1->id,
            'size_id' => $sizes['41'],
            'color_id' => $colors['Black'],
            'stock' => 7,
            'price' => 1000000,
        ]);
        ProductVariant::create([
            'product_id' => $product1->id,
            'size_id' => $sizes['42'],
            'color_id' => $colors['White'],
            'stock' => 3,
            'price' => 1050000,
        ]);

        $product2 = Product::create([
            'name' => 'Apple Watch Series 7',
            'slug' => Str::slug('Apple Watch Series 7'),
            'description' => 'Smartwatch canggih dengan fitur kesehatan dan kebugaran.',
            'price' => 4000000,
            'image' => 'sh2.jpg',
        ]);
        ProductVariant::create([
            'product_id' => $product2->id,
            'size_id' => null,
            'color_id' => $colors['Black'],
            'stock' => 10,
            'price' => 4000000,
        ]);
        ProductVariant::create([
            'product_id' => $product2->id,
            'size_id' => null,
            'color_id' => $colors['Red'],
            'stock' => 5,
            'price' => 4100000,
        ]);

        $product3 = Product::create([
            'name' => 'Wireless Headphones X9',
            'slug' => Str::slug('Wireless Headphones X9'),
            'description' => 'Headphone nirkabel dengan kualitas suara jernih dan bass mendalam.',
            'price' => 1000000,
            'image' => 'sh3.jpg',
        ]);
        ProductVariant::create([
            'product_id' => $product3->id,
            'size_id' => null,
            'color_id' => $colors['Black'],
            'stock' => 12,
            'price' => 1000000,
        ]);
        ProductVariant::create([
            'product_id' => $product3->id,
            'size_id' => null,
            'color_id' => $colors['White'],
            'stock' => 8,
            'price' => 1000000,
        ]);

        $product4 = Product::create([
            'name' => 'Colorful Sneakers Pro',
            'slug' => Str::slug('Colorful Sneakers Pro'),
            'description' => 'Sepatu kasual dengan warna-warna cerah dan desain trendi.',
            'price' => 1000000,
            'image' => 'sh4.jpg',
        ]);
        ProductVariant::create([
            'product_id' => $product4->id,
            'size_id' => $sizes['38'],
            'color_id' => $colors['Red'],
            'stock' => 2,
            'price' => 1000000,
        ]);
        ProductVariant::create([
            'product_id' => $product4->id,
            'size_id' => $sizes['39'],
            'color_id' => $colors['Red'],
            'stock' => 3,
            'price' => 1000000,
        ]);
        ProductVariant::create([
            'product_id' => $product4->id,
            'size_id' => $sizes['38'],
            'color_id' => $colors['Blue'],
            'stock' => 1,
            'price' => 1000000,
        ]);

        $product5 = Product::create([
            'name' => 'Vintage Polaroid Camera',
            'slug' => Str::slug('Vintage Polaroid Camera'),
            'description' => 'Kamera instan gaya retro, ciptakan kenangan klasik.',
            'price' => 3000000,
            'image' => 'sh5.jpg',
        ]);
        ProductVariant::create([
            'product_id' => $product5->id,
            'size_id' => null,
            'color_id' => null,
            'stock' => 10,
            'price' => 3000000,
        ]);

        $product6 = Product::create([
            'name' => 'Curology Daily Cleanser',
            'slug' => Str::slug('Curology Daily Cleanser'),
            'description' => 'Pembersih wajah lembut untuk semua jenis kulit.',
            'price' => 500000,
            'image' => 'sh6.jpg',
        ]);
        ProductVariant::create([
            'product_id' => $product6->id,
            'size_id' => null,
            'color_id' => null,
            'stock' => 180,
            'price' => 500000,
        ]);

        $product7 = Product::create([
            'name' => 'Q&Q Modern Wall Clock',
            'slug' => Str::slug('Q&Q Modern Wall Clock'),
            'description' => 'Jam dinding minimalis modern untuk dekorasi rumah.',
            'price' => 1000000,
            'image' => 'sh7.jpg',
        ]);
        ProductVariant::create([
            'product_id' => $product7->id,
            'size_id' => null,
            'color_id' => $colors['Black'],
            'stock' => 50,
            'price' => 1000000,
        ]);
        ProductVariant::create([
            'product_id' => $product7->id,
            'size_id' => null,
            'color_id' => $colors['White'],
            'stock' => 40,
            'price' => 1000000,
        ]);

        $product8 = Product::create([
            'name' => 'Ergonomic Earphones Z1',
            'slug' => Str::slug('Ergonomic Earphones Z1'),
            'description' => 'Earphone in-ear dengan desain ergonomis dan suara detail.',
            'price' => 1000000,
            'image' => 'sh8.jpg',
        ]);
        ProductVariant::create([
            'product_id' => $product8->id,
            'size_id' => null,
            'color_id' => $colors['Black'],
            'stock' => 150,
            'price' => 1000000,
        ]);
        ProductVariant::create([
            'product_id' => $product8->id,
            'size_id' => null,
            'color_id' => $colors['Red'],
            'stock' => 100,
            'price' => 1000000,
        ]);

        $product9 = Product::create([
            'name' => 'Apple Watch Milanese Loop',
            'slug' => Str::slug('Apple Watch Milanese Loop'),
            'description' => 'Strap jam tangan mewah untuk Apple Watch.',
            'price' => 1000000,
            'image' => 'sh9.jpg',
        ]);
        ProductVariant::create([
            'product_id' => $product9->id,
            'size_id' => null,
            'color_id' => $colors['Silver'],
            'stock' => 60,
            'price' => 1000000,
        ]);
        ProductVariant::create([
            'product_id' => $product9->id,
            'size_id' => null,
            'color_id' => $colors['Black'],
            'stock' => 40,
            'price' => 1000000,
        ]);
    }
}
