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
        Schema::rename('invoices', 'sales');

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('sales', 'invoices');

        Schema::table('invoices', function (Blueprint $table) {
            string('invoice_number')->unique();
        });
    }
};
