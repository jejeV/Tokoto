<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JabodetabekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $now = Carbon::now();

        $provinces = [
            ['name' => 'DKI Jakarta', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Jawa Barat', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Banten', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('provinces')->insert($provinces);

        $jakartaId = DB::table('provinces')->where('name', 'DKI Jakarta')->value('id');
        $jawaBaratId = DB::table('provinces')->where('name', 'Jawa Barat')->value('id');
        $bantenId = DB::table('provinces')->where('name', 'Banten')->value('id');

        $cities = [
            ['province_id' => $jakartaId, 'name' => 'Kota Jakarta Pusat', 'created_at' => $now, 'updated_at' => $now],
            ['province_id' => $jakartaId, 'name' => 'Kota Jakarta Utara', 'created_at' => $now, 'updated_at' => $now],
            ['province_id' => $jakartaId, 'name' => 'Kota Jakarta Barat', 'created_at' => $now, 'updated_at' => $now],
            ['province_id' => $jakartaId, 'name' => 'Kota Jakarta Selatan', 'created_at' => $now, 'updated_at' => $now],
            ['province_id' => $jakartaId, 'name' => 'Kota Jakarta Timur', 'created_at' => $now, 'updated_at' => $now],
            ['province_id' => $jakartaId, 'name' => 'Kabupaten Kepulauan Seribu', 'created_at' => $now, 'updated_at' => $now],

            ['province_id' => $jawaBaratId, 'name' => 'Kota Bogor', 'created_at' => $now, 'updated_at' => $now],
            ['province_id' => $jawaBaratId, 'name' => 'Kabupaten Bogor', 'created_at' => $now, 'updated_at' => $now],
            ['province_id' => $jawaBaratId, 'name' => 'Kota Depok', 'created_at' => $now, 'updated_at' => $now],
            ['province_id' => $jawaBaratId, 'name' => 'Kota Bekasi', 'created_at' => $now, 'updated_at' => $now],
            ['province_id' => $jawaBaratId, 'name' => 'Kabupaten Bekasi', 'created_at' => $now, 'updated_at' => $now],

            ['province_id' => $bantenId, 'name' => 'Kota Tangerang', 'created_at' => $now, 'updated_at' => $now],
            ['province_id' => $bantenId, 'name' => 'Kabupaten Tangerang', 'created_at' => $now, 'updated_at' => $now],
            ['province_id' => $bantenId, 'name' => 'Kota Tangerang Selatan', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('cities')->insert($cities);
    }
}
