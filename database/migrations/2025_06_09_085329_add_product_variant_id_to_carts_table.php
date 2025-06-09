<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Pastikan ini ada

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('carts')) {
            throw new \Exception("Table 'carts' does not exist. Please run 'create_carts_table' migration first.");
        }

        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'user_id')) { 
                $query = DB::raw("
                    SELECT CONSTRAINT_NAME
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = '".DB::connection()->getDatabaseName()."'
                    AND TABLE_NAME = 'carts'
                    AND COLUMN_NAME = 'user_id'
                    AND REFERENCED_TABLE_NAME = 'users'
                ")->getValue(DB::connection()->getQueryGrammar());

                $foreignKeys = DB::select($query);

                foreach ($foreignKeys as $fk) {
                    $table->dropForeign($fk->CONSTRAINT_NAME);
                }
            }
        });

        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'selected_size') && Schema::hasColumn('carts', 'selected_color')) { // Pastikan kolom-kolom ini masih ada
                $query = DB::raw("
                    SELECT DISTINCT INDEX_NAME
                    FROM information_schema.STATISTICS
                    WHERE TABLE_SCHEMA = '".DB::connection()->getDatabaseName()."'
                    AND TABLE_NAME = 'carts'
                    AND INDEX_NAME IN ('user_product_variant_unique', 'session_product_variant_unique')
                ")->getValue(DB::connection()->getQueryGrammar());

                $uniqueIndexes = DB::select($query);

                foreach ($uniqueIndexes as $index) {
                    $table->dropUnique($index->INDEX_NAME);
                }
            }

            $table->unsignedBigInteger('product_variant_id')->nullable()->after('product_id');

            if (Schema::hasColumn('carts', 'selected_size')) {
                $table->dropColumn('selected_size');
            }
            if (Schema::hasColumn('carts', 'selected_color')) {
                $table->dropColumn('selected_color');
            }

            if (Schema::hasColumn('carts', 'user_id')) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('product_variant_id');

            $table->string('selected_size')->nullable()->after('product_id');
            $table->string('selected_color')->nullable()->after('selected_size');

            $table->unique(['user_id', 'product_id', 'selected_size', 'selected_color'], 'user_product_variant_unique');
            $table->unique(['session_id', 'product_id', 'selected_size', 'selected_color'], 'session_product_variant_unique');
        });
    }
};
