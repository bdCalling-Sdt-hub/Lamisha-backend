<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocuemtnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'resume' => 'required|file',
            'license_certification' => 'required|file',
            'libability_insurnce' => 'required|file',
            'buisness_formations_doc' => 'required|file',
            'enform' => 'required|file',
            'currrent_driver_license' => 'required|file',
            'current_cpr_certification' => 'required|file',
            'blood_bron_pathogen_certificaton' => 'required|file',
            'training_hipaa_osha' => 'required|file',
            // 'management_service_aggriment' => 'required|file',
            // 'nda' => 'required|file',
            // 'deligation_aggriment' => 'required|file',
            // 'ach_fomr' => 'required|file',
            // 'appoinment_date' => 'required',
            // 'appoinment_time' => 'required',
        ];   
    }
}
