<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\ContactMail;
use App\Mail\TiarMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function contact_mail(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'subject' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required'
        ]);

        $superAdmin = 'info@FindaMD4Me.com';
        $first_name = $validatedData['first_name'];
        $last_name = $validatedData['last_name'];
        $phone = $validatedData['phone'];
        $subject = $validatedData['subject'];
        $email = $validatedData['email'];
        $sms = $validatedData['message'];

        try {
            Mail::to($superAdmin)->send(new ContactMail($first_name, $last_name, $phone, $subject, $email, $sms));
            return response()->json(['status' => '200', 'message' => 'Mail sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => '500', 'message' => 'Mail sending failed', 'error' => $e->getMessage()]);
        }
    }

    public function coustom_trial(Request $request)
    {
        $superAdmin = 'info@FindaMD4Me.com';
        $email = $request->email;
        $trial = $request->trial;
        try {
            Mail::to($superAdmin)->send(new TiarMail( $email, $trial));
            return response()->json(['status' => '200', 'message' => 'Mail sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => '500', 'message' => 'Mail sending failed', 'error' => $e->getMessage()]);
        }
    }
}
