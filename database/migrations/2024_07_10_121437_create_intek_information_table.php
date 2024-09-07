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
        Schema::create('intek_information', function (Blueprint $table) {
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
            $table->string('buisness_name');
            $table->string('buisness_address');
            $table->string('how_long_time_buisness');
            $table->string('business_malpractice_insurance');
            $table->string('business_registe_red_secretary_state');
            $table->string('what_state_your_business_registered');
            $table->string('owns_the_company');
            $table->string('direct_service_business');
            $table->string('what_state_anicipate_service');
            $table->string('tier_service_interrested');
            $table->string('how_many_client_patients_service_month');
            $table->string('additional_question');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intek_information');
    }
};
