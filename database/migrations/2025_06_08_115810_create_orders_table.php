<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->string('order_number')->unique();

            $table->decimal('total_amount', 15, 2);
            $table->decimal('subtotal_amount', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0.00);

            $table->string('shipping_method');
            $table->decimal('shipping_cost', 15, 2)->default(0.00);

            $table->string('payment_method');
            $table->string('order_status')->default('pending');
            $table->string('payment_status')->default('pending');

            $table->string('midtrans_snap_token')->nullable();
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('midtrans_payment_type')->nullable();
            $table->decimal('midtrans_gross_amount', 15, 2)->nullable();
            $table->string('midtrans_masked_card')->nullable();
            $table->string('midtrans_bank')->nullable();
            $table->json('midtrans_va_numbers')->nullable();
            $table->string('payment_redirect_url')->nullable();

            $table->string('billing_first_name');
            $table->string('billing_last_name');
            $table->string('billing_email')->nullable();
            $table->string('billing_phone', 20);
            $table->string('billing_address_line_1');
            $table->string('billing_address_line_2')->nullable();
            $table->foreignId('billing_province_id')->nullable()->constrained('provinces')->onDelete('set null');
            $table->foreignId('billing_city_id')->nullable()->constrained('cities')->onDelete('set null');
            $table->string('billing_zip_code', 10);

            $table->string('shipping_first_name');
            $table->string('shipping_last_name');
            $table->string('shipping_email')->nullable();
            $table->string('shipping_phone_number', 20);
            $table->string('shipping_address_line_1');
            $table->string('shipping_address_line_2')->nullable();
            $table->foreignId('shipping_province_id')->nullable()->constrained('provinces')->onDelete('set null');
            $table->foreignId('shipping_city_id')->nullable()->constrained('cities')->onDelete('set null');
            $table->string('shipping_zip_code', 10);
            $table->string('shipping_status')->default('pending_shipment');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
