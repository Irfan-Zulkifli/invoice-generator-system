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
        Schema::rename('invoice_items', 'sale_items');

        Schema::table('sale_items', function (Blueprint $table) {
            $table->renameColumn('invoice_id', 'sale_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('sale_items', 'invoice_items');

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->renameColumn('sale_id', 'invoice_id');
        });
    }
};
