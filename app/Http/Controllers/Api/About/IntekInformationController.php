<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AppointmentRequest;
use App\Http\Requests\BuisnessRequest;
use App\Http\Requests\ParsonalRequest;
use App\Models\Parsonal;
use App\Models\Appoinment;
use App\Models\BuisnessInfo;
use DB;
use App\Mail\PersonalInfoMail;
use Mail;
use App\Models\User;
use App\Notifications\IntakInfoNotification;
class IntekInformationController extends Controller
{
    public function parsonal_info(Request $request)
    {
        $email = $request->email;
        $valiteUser = Parsonal::where('email', $email)->first();
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'email' => 'required|email|unique:parsonals,email,' . optional($valiteUser)->id,
            'phone' => 'required|string|max:20',
            'occupation' => 'required|string|max:255',
            'state_license_certificate' => 'required|string|max:500',
            'license_certificate_no' => 'required|string|max:255',
            'completed_training_certificate_service' => 'required|string|max:255',
            'mailing_address' => 'required|string|max:500',
        ]);
        if (isset($validated['state_license_certificate']) && is_array($validated['state_license_certificate'])) {
            $validated['state_license_certificate'] = json_encode($validated['state_license_certificate']);
        }

        $parsonal = Parsonal::updateOrCreate(
            ['email' => $validated['email']],
            $validated
        );
        if ($parsonal) {
            $parsonal->notify(new IntakInfoNotification('Intake Information', $parsonal));
            return response()->json(['status' => 200, 'message' => 'Data inserted/updated successfully', 'data' => $parsonal], 201);
        } else {
            return response()->json(['status' => 500, 'message' => 'Data insertion failed'], 500);
        }
    }

    public function buisness_info(BuisnessRequest $request)
    {
       $what_state_anicipate_service= array($request->what_state_anicipate_service);
        $validated = $request->validated();
        if (isset($validated['what_state_your_business_registered']) && is_array($validated['what_state_your_business_registered'])) {
            $validated['what_state_your_business_registered'] = json_encode($validated['what_state_your_business_registered']);
        }
        elseif (isset($validated['what_state_anicipate_service']) && is_array($validated['what_state_anicipate_service'])) {
            $validated['what_state_anicipate_service'] = json_encode($validated['what_state_anicipate_service']);
        }elseif(isset($validated['direct_service_business']) && is_array($validated['direct_service_business'])){
            $validated['direct_service_business'] = json_encode($validated['direct_service_business']);
        }
         $existing_user = DB::table('parsonals')->where('id', $request->parsonal_id)->first();

        if (! $existing_user) {
            return response()->json(['status'=>409,'message' => 'Plese filup parsonal inforamtion', 'data' => $existing_user], 409);
        } else {
            $inserted_id = DB::table('buisness_infos')->insertGetId($validated);

            if ($inserted_id) {
                $new_data = DB::table('buisness_infos')->where('id', $inserted_id)->first();

                Mail::to('signup@FindaMD4Me.com')->send(new PersonalInfoMail($existing_user->first_name, $existing_user->last_name,$existing_user->email, $existing_user->phone));
                return response()->json(['status'=>200,'message' => 'Data inserted successfully', 'data' => $new_data], 201);
            } else {
                return response()->json([ 'status'=>500,'message' => 'Data insertion failed'], 500);
            }
        }
    }

    public function appointment_info(AppointmentRequest $request)
    {
        $validated = $request->validated();
         $existing_user = Parsonal::find($request->parsonal_id);

        if (!$existing_user) {
            return response()->json(['status' => 409, 'message' => 'Please fill up personal information', 'data' => $existing_user], 409);
        } else {
            $appointment = Appoinment::create($validated);
            if ($appointment) {
                return response()->json(['status' => 200, 'message' => 'Data inserted successfully', 'data' => $appointment], 201);
            } else {
                return response()->json(['status' => 500, 'message' => 'Data insertion failed'], 500);
            }
        }
    }


}
