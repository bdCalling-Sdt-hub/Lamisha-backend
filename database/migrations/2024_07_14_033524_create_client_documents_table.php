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
        Schema::create('client_documents', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('status')->default('pending');
            $table->string('resume');
            $table->string('license_certification');           
            $table->string('libability_insurnce');
            $table->string('buisness_formations_doc');
            $table->string('enform');
            $table->string('currrent_driver_license');
            $table->string('current_cpr_certification');
            $table->string('blood_bron_pathogen_certificaton');
            $table->string('training_hipaa_osha');
            $table->string('management_service_aggriment')->nullable();
            $table->string('nda')->nullable();
            $table->string('deligation_aggriment')->nullable();
            $table->string('ach_fomr')->nullable();
            $table->string('member_ship_contact')->nullable(); 
            $table->string('date')->nullable();
            $table->string('time')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_documents');
    }
};
