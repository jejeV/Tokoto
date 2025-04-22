<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['bank_transfer', 'credit_card', 'ewallet', 'qris']);
            $table->string('midtrans_order_id')->nullable()->comment('Order ID dari Midtrans');
            $table->string('midtrans_transaction_status')->nullable()->comment('Status transaksi dari Midtrans');
            $table->decimal('shipping_cost', 10, 2)->nullable()->comment('Ongkir dari RajaOngkir');
            $table->string('shipping_service')->nullable()->comment('Jasa pengiriman (JNE, TIKI, dll)');
            $table->string('tracking_number')->nullable()->comment('Nomor resi pengiriman');
            $table->text('address');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
