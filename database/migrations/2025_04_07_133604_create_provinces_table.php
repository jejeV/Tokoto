<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->integer('id')->primary()->comment('ID Provinsi dari RajaOngkir');
            $table->string('name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('provinces');
    }
};
