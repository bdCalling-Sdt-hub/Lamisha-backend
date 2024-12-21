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
        Schema::create('buisness_infos', function (Blueprint $table) {
            $table->id();
            $table->integer('parsonal_id');
            $table->string('status')->default('pending');;
            $table->string('buisness_name');
            $table->string('client_type');
            $table->string('buisness_address');
            $table->string('how_long_time_buisness');
            $table->string('business_malpractice_insurance');
            $table->string('business_registe_red_secretary_state');
            $table->json('what_state_your_business_registered');
            $table->string('owns_the_company');
            $table->json('direct_service_business');
            $table->json('what_state_anicipate_service');
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
        Schema::dropIfExists('buisness_infos');
    }
};
