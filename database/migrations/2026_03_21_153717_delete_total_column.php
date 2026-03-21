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
        Schema::table('sales', function(Blueprint $table) {
            $table->dropColumn('total_amount');
        });

        Schema::table('sale_items', function(Blueprint $table) {
            $table->dropColumn(['subtotal', 'unit_price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function(Blueprint $table) {
            $table->decimal('total_amount', 10, 2)->after('user_id');
        });

        Schema::table('sale_items', function(Blueprint $table) {
            $table->decimal('unit_price', 10, 2)->after('quantity');
            $table->decimal('subtotal', 10, 2)->after('unit_price');
        });
    }
};
