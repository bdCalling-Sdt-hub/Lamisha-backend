<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntekInformation extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_nam',
        'last_name',
        'dob',
        'email',
        'phone',
        'occupation',
        'state_license_certificate',
        'license_certificate_no',
        'completed_training_certificate_service',
        'buisness_name',
        'buisness_address',
        'mailing_address',
        'how_long_time_buisness',
        'business_malpractice_insurance',
        'business_registe_red_secretary_state',
        'what_state_your_business_registered',
        'owns_the_company',
        'what_type_of_entity',
        'direct_service_business',
        'what_state_anicipate_service',
        'tier_service_interrested',
        'client_type',
        'how_many_client_patients_service_month',
        'additional_question',
        'date',
        'time'

    ];
    // public function getAttributeStateLicenseCertificate($value) {
    //     return json_decode($value);
    // }

    // public function setAttributeStateLicenseCertificate($value) {
    //     return json_encode($value);
    // }
}
