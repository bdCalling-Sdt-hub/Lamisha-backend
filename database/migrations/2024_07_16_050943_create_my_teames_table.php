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
        Schema::create('my_teames', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('status')->default('pending');            
            $table->string('first_name');
            $table->string('last_name');
            $table->string('dob');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('Role');
            $table->string('license_certificate_number');
            $table->string('addisional_certificate')->nullable();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_teames');
    }
};
