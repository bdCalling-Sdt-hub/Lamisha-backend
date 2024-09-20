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
    public function parsonal_info(ParsonalRequest $request)
    {
        $validated = $request->validated();
         $existing_user = DB::table('parsonals')->where('email', $validated['email'])->first();
         if (isset($validated['state_license_certificate']) && is_array($validated['state_license_certificate'])) {
            $validated['state_license_certificate'] = json_encode($validated['state_license_certificate']);
        }
        if ($existing_user) {
            return response()->json(['status' => 409, 'message' => 'We already have a intake on file with this email please contact us at info@findamd4me.com', 'data' => $existing_user], 409);
        } else {
            $inserted_id = DB::table('parsonals')->insertGetId($validated);

            if ($inserted_id) {
                $new_data = DB::table('parsonals')->where('id', $inserted_id)->first();

                // Send notification
                $parsonal = Parsonal::find($inserted_id);
                if ($parsonal) {
                    $parsonal->notify(new IntakInfoNotification('Intake Information', $new_data));
                }

                return response()->json(['status' => 200, 'message' => 'Data inserted successfully', 'data' => $new_data], 201);
            } else {
                return response()->json(['status' => 500, 'message' => 'Data insertion failed'], 500);
            }
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
