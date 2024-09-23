<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\ContactMail;
use App\Mail\TiarMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function contact_mail(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'subject' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required'
        ]);

        $validator = Validator::make($request->all(),[

            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'subject' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required',

        ]);

        if ($validator->fails()){
            return response()->json(["errors"=>$validator->errors()],400);
        }

        $mailData=[
            'first_name' => $request->first_name,
            'last_name'=>$request->last_name,
            'phone' => $request->phone,
            'subject'=>$request->subject,
            'email'=>$request->email,
            'sms'=>$request->message
           ];

        try {
            Mail::to('info@findamd4me.com')->send(new ContactMail($mailData));
            return response()->json(['status' => '200', 'message' => 'Mail sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => '500', 'message' => 'Mail sending failed', 'error' => $e->getMessage()]);
        }
    }

    public function coustom_trial(Request $request)
    {
        $superAdmin = 'info@findamd4me.com';
        $email = $request->email;
        $trial = $request->trial;
        try {
            Mail::to($superAdmin)->send(new TiarMail( 'info@findamd4me.com', $trial));
            return response()->json(['status' => '200', 'message' => 'Mail sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => '500', 'message' => 'Mail sending failed', 'error' => $e->getMessage()]);
        }
    }
}
