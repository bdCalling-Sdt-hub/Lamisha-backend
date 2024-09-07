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

        if ($existing_user) {
            return response()->json(['status' => 409, 'message' => 'We already have a intake on file with this email please contact us at'. $request->eamil, 'data' => $existing_user], 409);
        } else {
            $inserted_id = DB::table('parsonals')->insertGetId($validated);

            if ($inserted_id) {
                $new_data = DB::table('parsonals')->where('id', $inserted_id)->first();

                Mail::to('signup@FindaMD4Me.com')->send(new PersonalInfoMail($request->first_name, $request->last_name, $request->email, $request->phone));

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


    // public function parsonal_info(ParsonalRequest $request)
    // {
    //    // return $request->all();

    //     $validated = $request->validated();
    //     $existing_user = DB::table('parsonals')->where('email', $validated['email'])->first();
    //     if ($existing_user) {
    //         return response()->json(['status'=>409, 'message' => 'User already exists', 'data' => $existing_user], 409);
    //     } else {
    //         $inserted_id = DB::table('parsonals')->insertGetId($validated);
    //         if ($inserted_id) {
    //             $new_data = DB::table('parsonals')->where('id', $inserted_id)->first();
    //             SentIntakeInfo::dispath($validated);
    //             return response()->json(['status'=>200, 'message' => 'Data inserted successfully', 'data' => $new_data], 201);
    //         } else {
    //             return response()->json(['status'=>500,'message' => 'Data insertion failed'], 500);
    //         }
    //     }

    // }

    // Client update buisness info

    public function buisness_info(BuisnessRequest $request)
    {
       $what_state_anicipate_service= array($request->what_state_anicipate_service);
        $validated = $request->validated();
         $existing_user = DB::table('parsonals')->where('id', $request->parsonal_id)->first();
        if (! $existing_user) {
            return response()->json(['status'=>409,'message' => 'Plese filup parsonal inforamtion', 'data' => $existing_user], 409);
        } else {
            $inserted_id = DB::table('buisness_infos')->insertGetId($validated);
            // $inserted_id = BuisnessInfo::create([
            // 'parsonal_id'=>$request->parsonal_id,
            // 'buisness_name'=>$request->buisness_name,
            // 'client_type'=>$request->client_type,
            // 'buisness_address'=>$request->buisness_address,
            // 'how_long_time_buisness'=>$request->how_long_time_buisness,
            // 'business_malpractice_insurance'=>$request->business_malpractice_insurance,
            // 'business_registe_red_secretary_state'=>$request->business_registe_red_secretary_state,
            // 'what_state_your_business_registered'=>$request->what_state_your_business_registered,
            // 'owns_the_company'=>$request->owns_the_company,
            // 'direct_service_business'=>$request->direct_service_business,
            // 'what_state_anicipate_service'=>json_encode($what_state_anicipate_service),
            // 'tier_service_interrested'=>$request->tier_service_interrested,
            // 'how_many_client_patients_service_month'=>$request->how_many_client_patients_service_month,
            // 'additional_question'=>$request->additional_question,
            // ]);
            if ($inserted_id) {
                $new_data = DB::table('buisness_infos')->where('id', $inserted_id)->first();
                return response()->json(['status'=>200,'message' => 'Data inserted successfully', 'data' => $new_data], 201);
            } else {
                return response()->json([ 'status'=>500,'message' => 'Data insertion failed'], 500);
            }
        }
    }


    public function appointment_info(AppointmentRequest $request)
    {
        $validated = $request->validated();

        // Check if the personal information exists
         $existing_user = Parsonal::find($request->parsonal_id);

        if (!$existing_user) {
            return response()->json(['status' => 409, 'message' => 'Please fill up personal information', 'data' => $existing_user], 409);
        } else {
            // Create a new appointment
            $appointment = Appoinment::create($validated);

            if ($appointment) {
                return response()->json(['status' => 200, 'message' => 'Data inserted successfully', 'data' => $appointment], 201);
            } else {
                return response()->json(['status' => 500, 'message' => 'Data insertion failed'], 500);
            }
        }
    }


}
