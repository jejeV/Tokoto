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

        // Product 1: sh1-airmax1
        $product1 = Product::create([
            'name' => 'Nike Airmax 1',
            'slug' => Str::slug('Nike Airmax 1'),
            'description' => 'Sepatu Nike Airmax 1, ringan dan nyaman untuk gaya sehari-hari.',
            'price' => 1200000,
            'image' => 'sh1-airmax1.jpg',
        ]);
        ProductVariant::create([
            'product_id' => $product1->id,
            'size_id' => $sizes['40'],
            'color_id' => $colors['Black'],
            'stock' => 10,
            'price' => 1200000,
        ]);
        ProductVariant::create([
            'product_id' => $product1->id,
            'size_id' => $sizes['41'],
            'color_id' => $colors['Black'],
            'stock' => 12,
            'price' => 1200000,
        ]);
        ProductVariant::create([
            'product_id' => $product1->id,
            'size_id' => $sizes['42'],
            'color_id' => $colors['White'],
            'stock' => 8,
            'price' => 1250000,
        ]);

        $product2 = Product::create([
            'name' => 'Nike Airmax 90G',
            'slug' => Str::slug('Nike Airmax 90G'),
            'description' => 'Sepatu Nike Airmax 90G dengan desain klasik dan bantalan udara yang empuk.',
            'price' => 1350000,
            'image' => 'sh2-airmax90g.jpg',
        ]);
        ProductVariant::create([
            'product_id' => $product2->id,
            'size_id' => $sizes['40'],
            'color_id' => $colors['Black'],
            'stock' => 8,
            'price' => 1350000,
        ]);
        ProductVariant::create([
            'product_id' => $product2->id,
            'size_id' => $sizes['41'],
            'color_id' => $colors['Black'],
            'stock' => 10,
            'price' => 1350000,
        ]);
        ProductVariant::create([
            'product_id' => $product2->id,
            'size_id' => $sizes['42'],
            'color_id' => $colors['Brown'],
            'stock' => 5,
            'price' => 1400000,
        ]);

        $product3 = Product::create([
            'name' => 'Nike Airmax DN8',
            'slug' => Str::slug('Nike Airmax DN8'),
            'description' => 'Sepatu Nike Airmax DN8, performa tinggi dengan gaya modern.',
            'price' => 1500000,
            'image' => 'sh3-airmaxdn8.jpg',
        ]);
        ProductVariant::create([
            'product_id' => $product3->id,
            'size_id' => $sizes['39'],
            'color_id' => $colors['Brown'],
            'stock' => 7,
            'price' => 1500000,
        ]);
        ProductVariant::create([
            'product_id' => $product3->id,
            'size_id' => $sizes['40'],
            'color_id' => $colors['Brown'],
            'stock' => 9,
            'price' => 1500000,
        ]);

        $product4 = Product::create([
            'name' => 'Nike Jordan',
            'slug' => Str::slug('Nike Jordan'),
            'description' => 'Sepatu ikonik Nike Jordan dengan desain stylish dan nyaman.',
            'price' => 1800000,
            'image' => 'sh4-jordan.jpg',
        ]);
        ProductVariant::create([
            'product_id' => $product4->id,
            'size_id' => $sizes['40'],
            'color_id' => $colors['Blue'],
            'stock' => 6,
            'price' => 1800000,
        ]);
        ProductVariant::create([
            'product_id' => $product4->id,
            'size_id' => $sizes['41'],
            'color_id' => $colors['Blue'],
            'stock' => 8,
            'price' => 1800000,
        ]);

        $product5 = Product::create([
            'name' => 'Nike JA 2',
            'slug' => Str::slug('Nike JA 2'),
            'description' => 'Sepatu Nike JA 2, dirancang untuk performa basket maksimal.',
            'price' => 1650000,
            'image' => 'sh5-ja2.jpg',
        ]);
        ProductVariant::create([
            'product_id' => $product5->id,
            'size_id' => $sizes['42'],
            'color_id' => $colors['Black'],
            'stock' => 5,
            'price' => 1650000,
        ]);
        ProductVariant::create([
            'product_id' => $product5->id,
            'size_id' => $sizes['43'],
            'color_id' => $colors['Black'],
            'stock' => 7,
            'price' => 1650000,
        ]);

        $product6 = Product::create([
            'name' => 'Nike Air Jordan 1',
            'slug' => Str::slug('Nike Air Jordan 1'),
            'description' => 'Sepatu klasik Nike Air Jordan 1, cocok untuk kolektor dan gaya streetwear.',
            'price' => 2000000,
            'image' => 'sh6-airjordan1.jpg',
        ]);
        ProductVariant::create([
            'product_id' => $product6->id,
            'size_id' => $sizes['41'],
            'color_id' => $colors['Blue'],
            'stock' => 4,
            'price' => 2000000,
        ]);
        ProductVariant::create([
            'product_id' => $product6->id,
            'size_id' => $sizes['42'],
            'color_id' => $colors['White'],
            'stock' => 3,
            'price' => 2050000,
        ]);

        $product7 = Product::create([
            'name' => 'Nike P6000',
            'slug' => Str::slug('Nike P6000'),
            'description' => 'Sepatu Nike P6000 dengan estetika Y2K yang menonjol.',
            'price' => 1100000,
            'image' => 'sh7-p6000.jpg',
        ]);
        ProductVariant::create([
            'product_id' => $product7->id,
            'size_id' => $sizes['39'],
            'color_id' => $colors['Black'],
            'stock' => 9,
            'price' => 1100000,
        ]);
        ProductVariant::create([
            'product_id' => $product7->id,
            'size_id' => $sizes['40'],
            'color_id' => $colors['Yellow'],
            'stock' => 11,
            'price' => 1100000,
        ]);

        $product8 = Product::create([
            'name' => 'Nike Dunk Low',
            'slug' => Str::slug('Nike Dunk Low'),
            'description' => 'Sepatu Nike Dunk Low, gaya ikonis yang tak lekang oleh waktu.',
            'price' => 1400000,
            'image' => 'sh8-dunk.jpg',
        ]);
        ProductVariant::create([
            'product_id' => $product8->id,
            'size_id' => $sizes['40'],
            'color_id' => $colors['Blue'],
            'stock' => 15,
            'price' => 1400000,
        ]);
        ProductVariant::create([
            'product_id' => $product8->id,
            'size_id' => $sizes['41'],
            'color_id' => $colors['Black'],
            'stock' => 13,
            'price' => 1400000,
        ]);

        $product9 = Product::create([
            'name' => 'Nike Dunk Low Nature',
            'slug' => Str::slug('Nike Dunk Low Nature'),
            'description' => 'Nike Dunk Low Nature, sepatu ramah lingkungan dengan sentuhan alami.',
            'price' => 1450000,
            'image' => 'sh9-dunknature.jpg',
        ]);
        ProductVariant::create([
            'product_id' => $product9->id,
            'size_id' => $sizes['38'],
            'color_id' => $colors['White'],
            'stock' => 10,
            'price' => 1450000,
        ]);
        ProductVariant::create([
            'product_id' => $product9->id,
            'size_id' => $sizes['39'],
            'color_id' => $colors['White'],
            'stock' => 8,
            'price' => 1450000,
        ]);
    }
}
