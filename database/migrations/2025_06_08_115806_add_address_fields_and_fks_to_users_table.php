<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom alamat
            $table->string('address_line_1')->nullable()->after('remember_token'); // Atur 'after' sesuai kolom terakhir Anda
            $table->string('address_line_2')->nullable()->after('address_line_1');
            $table->string('zip_code', 10)->nullable()->after('address_line_2');
            $table->string('phone_number', 20)->nullable()->after('zip_code');

            // Menambahkan foreign key ke provinces dan cities
            // Ini akan berhasil karena provinces dan cities sudah dibuat di migrasi sebelumnya.
            $table->foreignId('province_id')->nullable()->constrained('provinces')->onDelete('set null')->after('phone_number');
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('set null')->after('province_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu
            $table->dropForeign(['province_id']);
            $table->dropForeign(['city_id']);

            // Hapus kolom-kolom alamat
            $table->dropColumn([
                'address_line_1',
                'address_line_2',
                'province_id',
                'city_id',
                'zip_code',
                'phone_number',
            ]);
        });
    }
};
