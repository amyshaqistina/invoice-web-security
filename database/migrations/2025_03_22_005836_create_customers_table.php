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
        Schema::dropIfExists('customers');




        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Customer Name
            $table->string('email')->unique(); // Email
            $table->decimal('paid_to_date', 15, 2)->default(0); // Paid to Date
            $table->decimal('balance', 15, 2)->nullable(); // Balance after paid
            $table->dateTime('last_login')->nullable(); // Last Login
            $table->foreignId('team_id')->nullable(); // Team reference
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable(); // Gender
            $table->string('country')->default('Malaysia'); // Country (default to Malaysia)
            $table->timestamps(); // created_at & updated_at
            $table->softDeletes(); // Optional: Soft delete
        });

        // Schema::create('customers', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name'); // Customer Name
        //     $table->string('house_unit_no')->nullable(); // House/Unit No.
        //     $table->string('telephone_no')->nullable(); // Telephone No.
        //     $table->text('address')->nullable(); // Address
        //     $table->string('email')->unique(); // Email
        //     $table->string('city_district')->nullable(); // City/District
        //     $table->string('ic_mykad')->unique(); // IC/MyKad (National ID)
        //     $table->string('postal_code')->nullable(); // Postal Code
        //     $table->date('date_of_birth')->nullable(); // Date of Birth
        //     $table->enum('gender', ['Male', 'Female', 'Other'])->nullable(); // Gender
        //     $table->string('country')->default('Malaysia'); // Country (default to Malaysia)
        //     $table->text('notes_comments')->nullable(); // Notes/Comments
        //     $table->json('attachment')->nullable(); // File Attachment (stored as path)
        //     $table->foreignId('team_id')->nullable();
        //     $table->foreignId('user_id')->nullable();
        //     $table->softDeletes();
        //     $table->timestamps();
        // });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('customers');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
