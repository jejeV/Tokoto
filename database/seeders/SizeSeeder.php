<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Size;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Size::firstOrCreate(['name' => '36']);
        Size::firstOrCreate(['name' => '37']);
        Size::firstOrCreate(['name' => '38']);
        Size::firstOrCreate(['name' => '39']);
        Size::firstOrCreate(['name' => '40']);
        Size::firstOrCreate(['name' => '41']);
        Size::firstOrCreate(['name' => '42']);
        Size::firstOrCreate(['name' => '43']);
        Size::firstOrCreate(['name' => '44']);
        Size::firstOrCreate(['name' => 'One Size']);
    }
}
