<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('payments');



        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // Date of the payment
            $table->string('payment_number')->unique(); // Unique Payment Identification number
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade'); // Customer reference
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null'); // Optional Invoice reference
            $table->decimal('amount', 10, 2); // Payment Amount
            $table->enum('payment_mode', ['cash', 'check', 'credit_card', 'bank_transfer'])->nullable(); // Payment Mode
            $table->text('notes')->nullable(); // Additional payment details
            $table->foreignId('team_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
