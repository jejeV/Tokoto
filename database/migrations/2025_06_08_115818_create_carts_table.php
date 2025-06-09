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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('session_id')->nullable(); // Jangan jadikan unique jika ada beberapa item di keranjang tamu
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('selected_size')->nullable();
            $table->string('selected_color')->nullable();
            $table->integer('quantity')->default(1);
            $table->timestamps();

            // Memastikan setiap user/sesi hanya memiliki satu entri untuk kombinasi produk, ukuran, dan warna
            $table->unique(['user_id', 'product_id', 'selected_size', 'selected_color'], 'user_product_variant_unique');
            $table->unique(['session_id', 'product_id', 'selected_size', 'selected_color'], 'session_product_variant_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
