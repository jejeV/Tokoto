<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product; // Import model Product Anda

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::create([
            'name' => 'Nike Air Sneakers',
            'description' => 'Sepatu olahraga ringan dan nyaman dengan desain modern.',
            'price' => 45.00,
            'stock' => 150,
            'weight' => 500,
            'image' => 'sh1.jpg',
        ]);

        Product::create([
            'name' => 'Apple Watch Series 7',
            'description' => 'Smartwatch canggih dengan fitur kesehatan dan kebugaran.',
            'price' => 399.00,
            'stock' => 75,
            'weight' => 150,
            'image' => 'sh2.jpg',
        ]);

        Product::create([
            'name' => 'Wireless Headphones X9',
            'description' => 'Headphone nirkabel dengan kualitas suara jernih dan bass mendalam.',
            'price' => 120.00,
            'stock' => 200,
            'weight' => 300,
            'image' => 'sh3.jpg',
        ]);

        Product::create([
            'name' => 'Colorful Sneakers Pro',
            'description' => 'Sepatu kasual dengan warna-warna cerah dan desain trendi.',
            'price' => 55.00,
            'stock' => 120,
            'weight' => 480,
            'image' => 'sh4.jpg',
        ]);

        Product::create([
            'name' => 'Vintage Polaroid Camera',
            'description' => 'Kamera instan gaya retro, ciptakan kenangan klasik.',
            'price' => 89.99,
            'stock' => 60,
            'weight' => 400,
            'image' => 'sh5.jpg',
        ]);

        Product::create([
            'name' => 'Curology Daily Cleanser',
            'description' => 'Pembersih wajah lembut untuk semua jenis kulit.',
            'price' => 25.00,
            'stock' => 180,
            'weight' => 180,
            'image' => 'sh6.jpg',
        ]);

        Product::create([
            'name' => 'Q&Q Modern Wall Clock',
            'description' => 'Jam dinding minimalis modern untuk dekorasi rumah.',
            'price' => 30.00,
            'stock' => 90,
            'weight' => 600,
            'image' => 'sh7.jpg',
        ]);

        Product::create([
            'name' => 'Ergonomic Earphones Z1',
            'description' => 'Earphone in-ear dengan desain ergonomis dan suara detail.',
            'price' => 35.00,
            'stock' => 250,
            'weight' => 50,
            'image' => 'sh8.jpg',
        ]);

        Product::create([
            'name' => 'Apple Watch Milanese Loop',
            'description' => 'Strap jam tangan mewah untuk Apple Watch.',
            'price' => 79.00,
            'stock' => 100,
            'weight' => 80,
            'image' => 'sh9.jpg',
        ]);
       
    }
}
