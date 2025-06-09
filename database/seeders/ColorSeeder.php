<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Color;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Color::firstOrCreate(['name' => 'Black', 'hex_code' => '#000000']);
        Color::firstOrCreate(['name' => 'White', 'hex_code' => '#FFFFFF']);
        Color::firstOrCreate(['name' => 'Red', 'hex_code' => '#FF0000']);
        Color::firstOrCreate(['name' => 'Blue', 'hex_code' => '#0000FF']);
        Color::firstOrCreate(['name' => 'Green', 'hex_code' => '#008000']);
        Color::firstOrCreate(['name' => 'Yellow', 'hex_code' => '#FFFF00']);
        Color::firstOrCreate(['name' => 'Grey', 'hex_code' => '#808080']);
        Color::firstOrCreate(['name' => 'Pink', 'hex_code' => '#FFC0CB']);
        Color::firstOrCreate(['name' => 'Brown', 'hex_code' => '#A52A2A']);
        Color::firstOrCreate(['name' => 'Orange', 'hex_code' => '#FFA500']);
        Color::firstOrCreate(['name' => 'Silver', 'hex_code' => '#C0C0C0']);
    }
}
