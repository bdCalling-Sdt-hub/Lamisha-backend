<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillingRequest extends FormRequest
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
            'onoarding_fee'=>'required',
            'ach_payment'=>'required',
            'payment_date'=>'required',
            'vendor_ordering'=>'required',
            'appoinment_date'=>'required',
            'appoinment_time'=>'required',
        ];
    }
}
