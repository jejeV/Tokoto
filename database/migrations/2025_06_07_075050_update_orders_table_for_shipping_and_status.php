<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Digunakan untuk raw query ALTER TABLE ENUM

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('new_status', [
                'pending_payment', 'processing', 'packed', 'shipped',
                'delivered', 'completed', 'failed', 'cancelled'
            ])->default('pending_payment')->after('total_price');

            $table->string('shipping_name')->nullable()->after('new_status');
            $table->string('shipping_phone_number', 20)->nullable()->after('shipping_name');

            // Kolom untuk waktu pembayaran dan pengiriman
            $table->timestamp('payment_date')->nullable()->after('midtrans_transaction_status');
            $table->timestamp('delivered_at')->nullable()->after('tracking_number');
        });

        DB::statement("UPDATE orders SET new_status = 'pending_payment' WHERE status = 'pending'"); //
        DB::statement("UPDATE orders SET new_status = 'processing' WHERE status = 'paid' OR status = 'processing'"); //
        DB::statement("UPDATE orders SET new_status = 'shipped' WHERE status = 'shipped'"); //

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->renameColumn('new_status', 'status'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_name',
                'shipping_phone_number',
                'payment_date',
                'delivered_at',
            ]);

            $table->enum('old_status', ['pending','paid','processing','shipped'])
                  ->default('pending')->after('total_price');

            DB::statement("UPDATE orders SET old_status = 'pending' WHERE status = 'pending_payment'"); //
            DB::statement("UPDATE orders SET old_status = 'paid' WHERE status = 'processing'"); //
            DB::statement("UPDATE orders SET old_status = 'shipped' WHERE status = 'shipped'"); //

            $table->dropColumn('status');
            $table->renameColumn('old_status', 'status');
        });
    }
};
