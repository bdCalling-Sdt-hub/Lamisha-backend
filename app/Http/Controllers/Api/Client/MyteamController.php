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

        $license_certificate_number = null;
        if ($request->hasFile('license_certificate_number')) {
            $license_certificate_path = $request->file('license_certificate_number')->store('Team', 'public');
        } else {
            return response()->json(['message' => 'license_certificate_number upload failed'], 400);
        }

        $addisional_certificate = null;
        if ($request->hasFile('addisional_certificate')) {
            $addisional_certificate_path = $request->file('addisional_certificate')->store('Team', 'public');
        } else {
            return response()->json(['message' => 'addisional_certificate upload failed'], 400);
        }

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
        if($teame){
            return response()->json(['data' => $teame], 200); 
        }else{
            return response()->json(['message' => 'Add teame faile'], 200);  
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
            return response()->json(['data'=> $my_teame], 200);
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
                'status' => 'error',
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
            'status' => 'success',
            'message' => 'My teame Record deleted successfully',
        ]);
    }

    //---------------------------- Admin portal ------------------------ //


    public function show_all_team(Request $request)
    {
        $query = MyTeame::orderBy('id', 'desc');
        if ($request->filled('status')) {
            $query->where('status', 'like', "%{$request->status}%");
        }
        

        $all_data = $query->paginate(8);
      
        if($all_data){
           return response()->json(['status'=>'200', 'data' => $all_data], 200);
        }else{
           return response()->json(['status'=>'200', 'message' => 'Record not found'], 200);
        }
    }
    public function singel_team_member($id)
    {
        $singel_data = MyTeame::where('user_id', $id)->first();
      
        if($singel_data){
           return response()->json(['status'=>'200', 'data' => $singel_data], 200);
        }else{
           return response()->json(['status'=>'200', 'message' => 'Record not found'], 200);
        }
    }

    public function update_team_status(Request $request)
    {
        $client_id = MyTeame::find($request->id);
        if($client_id){
            $client_id->status = $request->status;
            $client_id->save();
            if($client_id){
                return response()->json(['status'=> '200','message'=> 'status update success']);
    
            }else{
                return response()->json(['status'=> '500','message'=> 'status update faile']);
            }
        }else{
            return response()->json(['status'=> '400','message'=> 'Record not found']);
        }
    }


    
    

}
