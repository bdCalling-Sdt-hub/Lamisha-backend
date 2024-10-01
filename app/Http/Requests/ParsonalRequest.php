<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParsonalRequest extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required',
            'dob' => 'required',
            'email' => 'required|email|unique:parsonals,email',
            'phone' => 'required|',
            'occupation' => 'required',
            'state_license_certificate' => 'required',
            'license_certificate_no' => 'required',
            'completed_training_certificate_service' => 'required',
            'mailing_address' => 'required',
        ];
    }


}
