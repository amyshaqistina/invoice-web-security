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
        Schema::dropIfExists('taxes');


        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Tax name (GST, VAT, etc.)
            $table->decimal('percentage', 5, 2); // Tax percentage (e.g., 10.00 for 10%)
            $table->text('description')->nullable(); // Optional description
            $table->boolean('is_compound')->default(false); // Is it a Compound Tax?
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
        Schema::dropIfExists('taxes');
    }
};
