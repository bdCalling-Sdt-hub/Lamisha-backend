<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\BillingRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\BillingMail;
use App\Http\Requests\DocuemtnRequest;
use App\Models\ClientDocument;
use Auth;
use App\Models\User;

class DocumentControler extends Controller
{
    public function billing(Request $request)
    {
        // Administrator email
        $admin_mail = 'engrabdurrahman4991@gmail.com';
    
        // Authenticated user email
        $auth_user = Auth::user();
        $email = $auth_user->email;

        $onoarding_fee_path = null;
        if ($request->hasFile('onoarding_fee')) {
            $onoarding_fee_path = $request->file('onoarding_fee')->store('PaymentHistory', 'public');
        } else {
            return response()->json(['message' => 'onboarding fee upload failed'], 400);
        }
        
        $ach_payment_path = null;
        if ($request->hasFile('ach_payment')) {
            $ach_payment_path = $request->file('ach_payment')->store('PaymentHistory', 'public');
        } else {
            return response()->json(['message' => 'ach payment upload failed'], 400);
        }

        $payment_date = $request->payment_date;

        $vendor_ordering_path = null;
        if ($request->hasFile('vendor_ordering')) {
            $vendor_ordering_path = $request->file('vendor_ordering')->store('PaymentHistory', 'public');
        } else {
            return response()->json(['message' => 'vendor ordering upload failed'], 400);
        }

        $appoinment_date = $request->appoinment_date;
        $appoinment_time = $request->appoinment_time;

        Mail::to($admin_mail)->send(new BillingMail($email, $onoarding_fee_path, $ach_payment_path, $payment_date, $vendor_ordering_path, $appoinment_date, $appoinment_time));
        return response()->json('sending your mail successfully');
    }

    public function store_document(Request $request)
    {
        $auth_user = Auth::user();
        $user_id = $auth_user->id;
        
        $files = [
            'resume',
            'license_certification',
            'libability_insurnce',
            'buisness_formations_doc',
            'enform',
            'currrent_driver_license',
            'current_cpr_certification',
            'blood_bron_pathogen_certificaton',
            'training_hipaa_osha',
            'management_service_aggriment',
            'nda',
            'deligation_aggriment',
            'ach_fomr',
            'member_ship_contact',
        ];
    
        $document = new ClientDocument();
        $document->user_id = $user_id;
    
        foreach ($files as $file) {
            if ($request->hasFile($file)) {
                $path = $request->file($file)->store('Client_documents', 'public');
                $document->$file = $path;
            } else {
                return response()->json(['message' => "$file upload failed"], 400);
            }
        }
    
        $document->save();
    
        return response()->json(['message' => 'Documents uploaded successfully'], 200);
    }

    public function show_user_documet(Request $request)
    {
        $query = ClientDocument::with('user')->orderBy('id', 'desc');
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
    public function singel_user_documet($id)
    {
        $singel_data = ClientDocument::where('user_id', $id)->first();
      
        if($singel_data){
           return response()->json(['status'=>'200', 'data' => $singel_data], 200);
        }else{
           return response()->json(['status'=>'200', 'message' => 'Record not found'], 200);
        }
    }

    public function client_document_status(Request $request)
    {
        $client_id = ClientDocument::find($request->id);
        $client_id->status = $request->status;
        $client_id->save();
        if($client_id){
            return response()->json(['status'=> '200','message'=> 'status update success']);

        }else{
            return response()->json(['status'=> '500','message'=> 'status update faile']);
        }
    }


    
}
