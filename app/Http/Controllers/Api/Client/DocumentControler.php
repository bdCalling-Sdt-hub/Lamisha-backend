<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use Illuminate\Http\Request;
use App\Http\Requests\BillingRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\BillingMail;
use App\Http\Requests\DocuemtnRequest;
use App\Models\ClientDocument;
use Auth;
use App\Models\User;

use App\Notifications\DocumentNotification;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Validator;

class DocumentControler extends Controller
{

    public function billing(Request $request)
    {
        $admin_mail = 'info@FindaMD4Me.com';

        // Authenticated user email
        $auth_user = Auth::user();
        $email = $auth_user->email;

        // Validate required file uploads and payment date
        $validator = Validator::make($request->all(), [
            'onoarding_fee' => 'required|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'ach_payment' => 'required|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'vendor_ordering' => 'required|file|mimes:pdf,jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['Validation Error', $validator->errors(), 422]);
        }

        // Check if files are received
        if (!$request->hasFile('onoarding_fee') || !$request->hasFile('ach_payment') || !$request->hasFile('vendor_ordering')) {
            return response()->json(['message' => 'One or more files are missing.'], 400);
        }

        // Store the uploaded files
        $onboarding_fee_path = $request->file('onoarding_fee')->store('PaymentHistory', 'public');
        $ach_payment_path = $request->file('ach_payment')->store('PaymentHistory', 'public');
        $vendor_ordering_path = $request->file('vendor_ordering')->store('PaymentHistory', 'public');

        // Send the billing email with the uploaded files
        Mail::to($admin_mail)->send(new BillingMail($email, $onboarding_fee_path, $ach_payment_path, $vendor_ordering_path));

        Billing::updateOrCreate([
            'user_id' => $auth_user->id,
            'onoarding_fee' => $onboarding_fee_path,
            'ach_payment' => $ach_payment_path,
            'vendor_ordering' => $vendor_ordering_path,
        ]);
        return response()->json(['status' => 200, 'message' => 'Billing information submitted and email sent successfully.']);
    }


    public function get_billing()
    {
        $auth_user = Auth::user();
        $billings = Billing::where('user_id', $auth_user->id)->first();
        if ($billings->isEmpty()) {
            return response()->json(['message' => 'No billing records found.'], 404);
        }

        return response()->json(['status' => 200, 'billings' => $billings], 200);
    }

    public function store_document(Request $request)
{


    $auth_user = Auth::user();
     $user_id = $auth_user->id;

    // Define the list of files
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
    ];

    // Check if the document already exists for the user
    $document = ClientDocument::where('user_id', $user_id)->first();

    if ($document) {
        // Unlink existing files
        foreach ($files as $file) {
            if (isset($document->$file) && \Storage::disk('public')->exists($document->$file)) {
                \Storage::disk('public')->delete($document->$file);
            }
        }
    } else {
        // Create a new document record if it doesn't exist
        $document = new ClientDocument();
        $document->user_id = $user_id;
    }

    // Process the uploaded files
    // foreach ($files as $file) {
    //     if ($request->hasFile($file)) {
    //         $path = $request->file($file)->store('Client_documents', 'public');
    //         $document->$file = $path;
    //     }
    // }

    foreach ($files as $file) {
        if ($request->hasFile($file)) {
            $uploadedFile = $request->file($file);
            $originalName = $uploadedFile->getClientOriginalName(); // Get the original file name
            $path = 'Client_documents/' . $originalName;

            // Store the file using the original name
            $uploadedFile->storeAs('Client_documents', $originalName, 'public');

            // Save the path
            $document->$file = $path;
        }
    }


    // Save the document record
    $document->save();

    // Return a success response
    return response()->json(['status' => 200, 'message' => 'Upload document successful', 'data' => $document], 200);
}


public function update_document(Request $request)
{
    $new_data = Auth::user();
    $user_id = $new_data->id;
    $files = [
        'management_service_aggriment',
        'nda',
        'deligation_aggriment',
        'ach_fomr',
        'member_ship_contact',
    ];

    // Find the document by ID
    $document = ClientDocument::find($request->id);
    if (!$document) {
        return response()->json(['status' => 400, 'message' => 'Document not found']);
    }

    // Update the document's user ID
    $document->user_id = $user_id;

    // Process file uploads
    // foreach ($files as $file) {
    //     if ($request->hasFile($file)) {
    //         $path = $request->file($file)->store('Client_documents', 'public');
    //         $document->$file = $path;
    //     } else {
    //         return response()->json(['status' => 400, 'message' => "$file upload failed"], 400);
    //     }
    // }
    foreach ($files as $file) {
        if ($request->hasFile($file)) {
            $uploadedFile = $request->file($file);
            $originalName = $uploadedFile->getClientOriginalName(); // Get the original file name

            // Define the storage path with the original file name
            $path = $uploadedFile->storeAs('Client_documents', $originalName, 'public');

            // Save the path to the database
            $document->$file = $path;
        } else {
            return response()->json(['status' => 400, 'message' => "$file upload failed"], 400);
        }
    }


    // Save the updated document
    $document->save();

    // Send notification to the user
    $new_data->notify(new DocumentNotification('Uploaded new document', $new_data));

    // Return response
    return response()->json(['status' => 200, 'data' => $document], 200);
}




    public function show_user_documet(Request $request)
    {
        $query = ClientDocument::with('user')->orderBy('id', 'desc');
        if ($request->filled('status')) {
            $query->where('status', 'like', "%{$request->status}%");
        }


        $all_data = $query->paginate(8);

        if($all_data){
           return response()->json(['status'=>200, 'data' => $all_data], 200);
        }else{
           return response()->json(['status'=>200, 'message' => 'Record not found'], 200);
        }
    }

    public function show_auth_user_documet(Request $request)
    {
        $auth = auth()->user();
        $query = ClientDocument::where('user_id', $auth->id)->first();

        if($query){
           return response()->json(['status'=>200, 'data' => $query], 200);
        }else{
           return response()->json(['status'=>200, 'message' => 'Record not found'], 200);
        }
    }




    public function singel_user_documet($id)
    {
        $singel_data = ClientDocument::where('id', $id)->with('user')->first();

        if($singel_data){
           return response()->json(['status'=>200, 'data' => $singel_data], 200);
        }else{
           return response()->json(['status'=>200, 'message' => 'Record not found'], 200);
        }
    }

    public function client_document_status(Request $request)
    {
        $client_id = ClientDocument::find($request->id);
        $client_id->status = $request->status;
        $client_id->save();
        if($client_id){
            return response()->json(['status'=> 200,'message'=> 'status update success']);

        }else{
            return response()->json(['status'=> 500,'message'=> 'status update faile']);
        }
    }

    public function updateDocumentAppoinment(Request $request){
         $auth_user = auth()->user()->id;
         if(!$auth_user){
            return response()->json(['status' => 400, 'message' => 'Unauthrize user'], 400);
         }
         $documentId = ClientDocument::where('user_id',$auth_user)->first();

         if(!$documentId){
            return response()->json(['status' => 400, 'message' => 'You have not document'], 400);
         }

        $document = ClientDocument::find($documentId->id);

        // Update the document details
        $document->date = $request->date;
        $document->time = $request->time;
        $document->status = "pending";

        // Save the document
        if (!$document->save()) {
            return response()->json(['status' => 500, 'message' => 'Failed to update appointment'], 500);
        }

        return response()->json(['status' => 200, 'message' => 'Appointment successfully scheduled']);
    }

    public function checkStatus()
    {
       $authUser = auth()->user()->id;
        $check = ClientDocument::where('user_id', $authUser)->where('status','approved')->first();
        if(!$check){
            return response()->json(['status'=>200, 'message'=>'Do not approve your document', 'data'=>$check],400);
        }
        return response()->json(['status'=>200, 'message'=>'Approve your document', 'data'=> $check]);
    }




}
