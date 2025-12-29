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
        Schema::dropIfExists('items');


        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('info')->nullable();
            $table->text('short_description')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->decimal('weight', 8, 3)->nullable();
            $table->integer('order_limit')->nullable();
            $table->integer('current_stock_balance')->default(0);
            $table->boolean('activate_ecommerce')->default(false);
            $table->boolean('activate_stock_management')->default(false);
            $table->boolean('activate_product_variations')->default(false);
            $table->string('directory')->nullable();
            $table->json('product_image')->nullable();
            $table->json('attachment')->nullable();
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
        Schema::dropIfExists('items');
    }
};
