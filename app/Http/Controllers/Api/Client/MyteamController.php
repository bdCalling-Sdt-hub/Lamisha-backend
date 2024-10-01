<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MyTeame;
use Auth;
use Illuminate\Support\Facades\Storage;
class MyteamController extends Controller
{
    //--------------------- Client protal --------------------------//

    public function store_teame(Request $request)
    {
        $auth_user = Auth::user();
        $user_id = $auth_user->id;

        // Validate the required fields
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'role' => 'required|string|max:50',
            'license_certificate_number' => 'required|file|mimes:pdf,jpeg,png|max:2048',
            'addisional_certificate' => 'nullable|file|mimes:pdf,jpeg,png|max:2048',
        ]);
        if (MyTeame::where('email', $request->email)->exists()) {
            return response()->json(['status' => 400, 'message' => 'Email already exists'], 400);
        }
        // Handle license certificate upload with original name
        $license_certificate_path = null;
        if ($request->hasFile('license_certificate_number')) {
            $licenseFile = $request->file('license_certificate_number');
            $licenseOriginalName = $licenseFile->getClientOriginalName();
            $license_certificate_path = $licenseFile->storeAs('Team', $licenseOriginalName, 'public');
        } else {
            return response()->json(['status' => 400, 'message' => 'License certificate upload failed'], 400);
        }

        // Handle additional certificate upload with original name
        $addisional_certificate_path = null;
        if ($request->hasFile('addisional_certificate')) {
            $addCertificateFile = $request->file('addisional_certificate');
            $addCertificateOriginalName = $addCertificateFile->getClientOriginalName();
            $addisional_certificate_path = $addCertificateFile->storeAs('Team', $addCertificateOriginalName, 'public');
        }

        // Create new team member
        $teame = new MyTeame();
        $teame->user_id = $user_id;
        $teame->first_name = $request->first_name;
        $teame->last_name = $request->last_name;
        $teame->dob = $request->dob;
        $teame->email = $request->email;
        $teame->phone = $request->phone;
        $teame->role = $request->role;
        $teame->license_certificate_number = $license_certificate_path;
        $teame->addisional_certificate = $addisional_certificate_path;
        $teame->save();

        // Check if save was successful
        if ($teame) {
            return response()->json(['status' => 200, 'message' => 'Team member added successfully', 'data' => $teame], 200);
        } else {
            return response()->json(['status' => 400, 'message' => 'Failed to add team member'], 400);
        }
    }


    public function show_my_team()
    {
        $auth_user = Auth::user();
        $user_id = $auth_user->id;
        $my_teame = MyTeame::where('user_id', $user_id)->paginate(8);
        if( $my_teame ){
            return response()->json(['data'=> $my_teame], 200);
        }else{
            return response()->json(['message'=> 'Record not found'], 400);
        }

    }

    public function single_team($id)
    {

        $my_teame = MyTeame::where('id', $id)->first();
        if( $my_teame ){
            return response()->json(['status'=>200,'data'=> $my_teame], 200);
        }else{
            return response()->json(['message'=> 'Record not found'], 400);
        }

    }


    public function delete_team(string $id)
    {
        // Find the MyTeame model instance by its ID
        $remove_teame_member = MyTeame::find($id);

        if (!$remove_teame_member) {
            return response()->json([
                'status' => 404,
                'message' => 'Record not found',
            ], 404);
        }

        // Get the path of the images
        $license_certificate_number = $remove_teame_member->license_certificate_number;
        $addisional_certificate = $remove_teame_member->addisional_certificate;

        // Construct full paths to the images
        $licenseCertificatePath = storage_path('app/public/' . $license_certificate_number);
        $additionalCertificatePath = storage_path('app/public/' . $addisional_certificate);

        // If the image path exists, unlink the image
        if ($license_certificate_number && file_exists($licenseCertificatePath)) {
            unlink($licenseCertificatePath);
        }
        if ($addisional_certificate && file_exists($additionalCertificatePath)) {
            unlink($additionalCertificatePath);
        }

        // Delete the MyTeame model instance
        MyTeame::where('id', $id)->delete();

        return response()->json([
            'status' => 200,
            'message' => 'My teame Record deleted successfully',
        ]);
    }

    //---------------------------- Admin portal ------------------------ //


    public function show_all_team(Request $request)
    {
        $query = MyTeame::with('user')->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('status', 'like', "%{$request->status}%");
        }

        // Paginate the results
        $all_data = $query->paginate(8);

        if ($all_data) {
            // Remove prefix from file paths if they exist
            $all_data->getCollection()->transform(function ($item) {
                if ($item->license_certificate_number) {
                    // Remove the prefix "Team/" or any specific structure you want
                    $item->license_certificate_number = str_replace('Team/', '', $item->license_certificate_number);
                }

                if ($item->addisional_certificate) {
                    // Remove the prefix "Team/" or any specific structure you want
                    $item->addisional_certificate = str_replace('Team/', '', $item->addisional_certificate);
                }

                return $item;
            });

            return response()->json(['status' => 200, 'data' => $all_data], 200);
        } else {
            return response()->json(['status' => 200, 'message' => 'Record not found'], 200);
        }
    }

    public function singel_team_member($id)
    {
        $singel_data = MyTeame::where('user_id', $id)->first();

        if($singel_data){
           return response()->json(['status'=>200, 'data' => $singel_data], 200);
        }else{
           return response()->json(['status'=>200, 'message' => 'Record not found'], 200);
        }
    }

    public function update_team_status(Request $request)
    {
        $client_id = MyTeame::find($request->id);
        if($client_id){
            $client_id->status = $request->status;
            $client_id->save();
            if($client_id){
                return response()->json(['status'=> 200,'message'=> 'status update success']);

            }else{
                return response()->json(['status'=> 500,'message'=> 'status update faile']);
            }
        }else{
            return response()->json(['status'=> 400,'message'=> 'Record not found']);
        }
    }





}
