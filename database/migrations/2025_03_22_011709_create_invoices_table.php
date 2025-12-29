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
        Schema::dropIfExists('invoices');



        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade'); // Customer reference
            $table->date('date'); // Invoice date
            $table->date('due_date'); // Due date
            $table->string('invoice_number')->nullable(); // Unique invoice number
            $table->decimal('discount', 10, 2)->default(0.00); // Total discount on the invoice
            $table->text('notes')->nullable(); // Additional notes
            $table->foreignId('team_id')->nullable();
            $table->foreignId('recurring_invoice_id')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('invoices');
    }
};
