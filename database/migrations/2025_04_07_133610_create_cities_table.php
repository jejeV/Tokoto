<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->integer('id')->primary()->comment('ID Kota/Kabupaten dari RajaOngkir');
            $table->integer('province_id');
            $table->string('name');
            $table->string('type')->comment('Kota/Kabupaten');
            $table->string('postal_code', 10);
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cities');
    }
};
