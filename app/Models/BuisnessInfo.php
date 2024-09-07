<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuisnessInfo extends Model
{
    use HasFactory;
    protected $fillable = [
        'parsonal_id',
        'buisness_name',
        'client_type',
        'buisness_address',
        'how_long_time_buisness',
        'business_malpractice_insurance',
        'business_registe_red_secretary_state',
        'what_state_your_business_registered',
        'owns_the_company',
        'direct_service_business',
        'what_state_anicipate_service',
        'tier_service_interrested',
        'how_many_client_patients_service_month',
        'additional_question',
    ];

    public function getWhatStateYourBusinessRegisteredAttribute($value) {
        return json_decode($value,true);
    }
    public function getWhatStateAnicipateServiceAttribute($value) {
        return json_decode($value,true);
    }
}
