<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuisnessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'parsonal_id'=>'required',
            'buisness_name'=>'required',
            'client_type'=>'required',
            'buisness_address'=>'required',            
            'how_long_time_buisness'=>'required',
            'business_malpractice_insurance'=>'required',
            'business_registe_red_secretary_state'=>'required',
            'what_state_your_business_registered'=>'required',
            'owns_the_company'=>'required',
            'direct_service_business'=>'required',
            'what_state_anicipate_service'=>'required',
            'tier_service_interrested'=>'required',
            'how_many_client_patients_service_month'=>'required',
            'additional_question'=>'required',
        ];
    }
}
