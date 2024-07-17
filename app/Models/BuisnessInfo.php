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
        'buisness_address',        
        'how_long_time_buisness',
        'business_malpractice_insurance',
        'business_registe_red_secretary_state',
        'what_state_your_business_registered',
        'owns_the_company',
        'what_type_of_entity',
        'direct_service_business',
        'what_state_anicipate_service',
        'tier_service_interrested',
        'how_many_client_patients_service_month',
        'additional_question',
    ];
}
