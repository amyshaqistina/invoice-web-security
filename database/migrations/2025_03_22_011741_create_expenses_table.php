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
        Schema::dropIfExists('expenses');


        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id'); // Expense Category reference
            $table->date('date'); // Date of expense
            $table->decimal('amount', 10, 2); // Expense amount
            $table->text('note')->nullable(); // Optional note
            $table->string('receipt')->nullable(); // Receipt file path (PDF/Image)
            $table->foreignId('team_id')->constrained()->onDelete('cascade')->nullable();
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
        Schema::dropIfExists('expenses');
    }
};
