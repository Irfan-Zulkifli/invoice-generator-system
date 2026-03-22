<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('sale_id');
            
            // 2. The Financials (Always use decimal for money!)
            $table->decimal('amount', 10, 2);
            
            // 3. The Details
            $table->date('payment_date');
            $table->string('payment_method'); // e.g., 'Cash', 'Bank Transfer', 'Cheque'
            $table->string('reference_number')->nullable(); // For receipt IDs or bank transaction numbers
            $table->text('notes')->nullable(); // Any extra info the admin wants to add
            
            $table->unsignedBigInteger('recorded_by');
            
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
