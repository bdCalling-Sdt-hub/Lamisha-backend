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
        Schema::create('parsonals', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('pending');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('dob');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('occupation');
            $table->string('mailing_address')->nullable();
            $table->json('state_license_certificate')->nullable();
            $table->string('license_certificate_no')->nullable();
            $table->string('completed_training_certificate_service');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parsonals');
    }
};
