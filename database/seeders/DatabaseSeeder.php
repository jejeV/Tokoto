<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            JabodetabekSeeder::class,
            SizeSeeder::class,
            ColorSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
