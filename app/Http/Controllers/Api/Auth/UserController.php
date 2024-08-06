<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Validator;
use Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtp;
use App\Mail\userCredinsial;
use App\Models\UpdateProfile;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{

    public function register(Request $request)
    {
        $user = User::where('email', $request->email)
            ->where('verify_email', 0)
            ->first();
        if ($user) {

            $random = Str::random(6);
            Mail::to($request->email)->send(new SendOtp($random));
            $user->update(['otp' => $random]);
            $user->update(['verify_email' => 0]);

            return response(['status'=>200,'message' => 'Please check your email for validate your email.'], 200);
        } else {
            Validator::extend('contains_dot', function ($attribute, $value, $parameters, $validator) {
                return strpos($value, '.') !== false;
            });

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|min:2|max:100',
                'last_name' => 'required|string|min:2|max:100',
                'email' => 'required|string|email|max:60|unique:users|contains_dot',
                'password' => 'required|string|min:6|confirmed',
                'user_type' => ['required', Rule::in(['USER', 'ADMIN', 'SUPER ADMIN'])],
            ], [
                'email.contains_dot' => 'without (.) Your email is invalid',
            ]);
            if ($validator->fails()) {
                return response()->json(['status'=>400,"message" => $validator->errors()], 400);
            }


            $userData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => $request->user_type,
                'otp' => Str::random(6),
                'verify_email' => 0,
                //'status'=>'active',

            ];

            $user = User::create($userData);

            Mail::to($request->email)->send(new userCredinsial($user->email, $request->password));

            return response()->json([
                'status'=> 200,
                'message' => 'Please check your email sending message ',
            ], 200);
        }
    }

    public function emailVerified(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], 422);
        }

        $user = User::where('otp', $request->otp)->first();

        if (!$user) {
            return response(['message' => 'Invalid OTP'], 422);
        }

        $user->update(['verify_email' => 1, 'otp' => 0]);

        $token = $user->createToken('Personal Access Token')->accessToken;

        return response([
            'status'=> 200,
            'message' => 'Email verified successfully',
            'token' => $token,
        ]);
    }

    public function forgetPassword(Request $request)
    {
        $email = $request->email;
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['error' => 'Email not found'], 401);
        } else if ($user->google_id != null || $user->apple_id != null) {
            return response()->json([
                'status'=>400,
                'message' => 'Your are social user, You do not need to forget password',
            ], 400);
        } else {
            $random = Str::random(6);
            Mail::to($request->email)->send(new SendOtp($random));
            $user->update(['otp' => $random]);
            $user->update(['verify_email' => 0]);
            return response()->json(['status'=>200, 'message' => 'Please check your email for get the OTP']);
        }
    }

    public function resetPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                "message" => "Your email is not exists"
            ], 401);
        }
        if (!$user->verify_email == 0) {
            return response()->json([
                "message" => "Your email is not verified"
            ], 401);
        }
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $user->update(['password' => Hash::make($request->password)]);
            return response()->json(['status'=>200,'message' => 'Password reset successfully','data'=> $user], 200);
        }
    }

    public function loginUser(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        // Check if the user account is active
        if ($user->status === 'active') {
            $token = $user->createToken('example')->accessToken;
            return response()->json(['status' => 200, 'token' => $token, 'data' => $user]);
        } else {
            return response()->json(['status' => 403, 'message' => 'Your account is deactivated'], 403);
        }
    } else {
        return response()->json(['status' => 401, 'message' => 'Unauthorized'], 401);
    }
}



    public function user()
    {
        $user = Auth::user();
        if ($user) {
            return response()->json(['status' => 200, 'user' => $user]);
        } else {
            return response()->json(['status' => 401, 'message' => 'No user authenticated'], 401);
        }
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|different:current_password',
                'confirm_password' => 'required|string|same:new_password',
            ]);

            if ($validator->fails()) {
                return response(['status'=>409, 'errors' => $validator->errors()], 409);
            }

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['status'=>409,'message' => 'Your current password is wrong'], 409);
            }

            $user->update(['password' => Hash::make($request->new_password)]);

            return response(['status'=>200, 'message' => 'Password updated successfully'], 200);
        } else {
            return response()->json(['status'=>401,'message' => 'You are not authorized!'], 401);
        }

    }

    public function resendOtp(Request $request)
    {
        $user = User::where('email', $request->email)
            //            ->where('verify_email', 0)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found or email already verified'], 404);
        }

        // Check if OTP resend is allowed (based on time expiration)
        $currentTime = now();
        $lastResentAt = $user->last_otp_sent_at; // Assuming you have a column in your users table to track the last OTP sent time

        // Define your expiration time (e.g., 5 minutes)
        $expirationTime = 5; // in minutes

        if ($lastResentAt && $lastResentAt->addMinutes($expirationTime)->isFuture()) {
            // Resend not allowed yet
            return response()->json(['message' => 'You can only resend OTP once every ' . $expirationTime . ' minutes'], 400);
        }

        // Generate new OTP
        $newOtp = Str::random(6);
        Mail::to($user->email)->send(new SendOtp($newOtp));

        // Update user data
        $user->update(['otp' => $newOtp]);
        $user->update(['last_otp_sent_at' => $currentTime]);

        return response()->json(['message' => 'OTP resent successfully']);
    }

    // Only user know update profile but basically behan the seen insert data update profile table then admin update date user profile
    public function post_update_profile(Request $request)
{
    $user = Auth::user();
    if ($user) {
        // Check if there is an existing update request for this user
        $existingRequest = UpdateProfile::where('user_id', $user->id)->first();

        if ($existingRequest) {
            // Delete the existing image file if it exists
            if ($existingRequest->image) {
                $existingImagePath = public_path($existingRequest->image);
                if (File::exists($existingImagePath)) {
                    File::delete($existingImagePath);
                }
            }
            // Delete the existing update request record
            $existingRequest->delete();
        }

        $newProfileUpdate = new UpdateProfile();
        $newProfileUpdate->user_id = $user->id;
        $newProfileUpdate->first_name = $request->first_name ?: $user->first_name;
        $newProfileUpdate->last_name = $request->last_name ?: $user->last_name;
        $newProfileUpdate->email = $request->email ?: $user->email;
        $newProfileUpdate->phone = $request->phone ?: $user->phone;
        $newProfileUpdate->buisness_address = $request->buisness_address ?: $user->buisness_address;
        $newProfileUpdate->buisness_name = $request->buisnes_name ?: $user->buisness_name;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $timeStamp = time(); // Current timestamp
            $fileName = $timeStamp . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/image', $fileName);

            $fileUrl = '/storage/image/' . $fileName;
            $newProfileUpdate->image = $fileUrl;
        } else {
            // Ensure the image field is null if no image is provided
            $newProfileUpdate->image = null;
        }

        $newProfileUpdate->save();

        return response()->json([
            'status' => 200,
            'message' => "Profile updated successfully, waiting for admin approval.",
            'data' => $newProfileUpdate,
        ]);
    } else {
        return response()->json([
            "message" => "You are not authorized!"
        ], 401);
    }
}



    public function update_profile_all_user()
    {
        $update_profile_user_ids = UpdateProfile::pluck('user_id');
        $users = User::whereIn('user_type', 'USER')->get();
        if ($users) {
            return response()->json(['status' => '200', 'data' => $users]);
        } else {
            return response()->json(['status' => '401', 'message' => 'User data not found'], 401);
        }
    }

    public function singel_user_update_profile_data($id)
    {
        $users = User::where('id', $id)->with('user_update')->first();
        if ($users) {
            return response()->json(['status' => '200', 'data' => $users]);
        } else {
            return response()->json(['status' => '401', 'message' => 'User data not found'], 401);
        }
    }

    public function update_user_status(Request $request)
    {
        $user = User::find($request->id);
        if ($user) {
            $user->status = $request->status;
            $user->save();
            if ($user) {
                return response()->json('User status update success', 200);
            } else {
                return response()->json(['message' => 'Status update fail'], 400);
            }
        } else {
            return response()->json(["message" => "Record not found"], 400);
        }

    }

    public function updateProfileStatus(Request $request)
    {
        $user = UpdateProfile::find($request->id);
        if ($user) {
            $user->status = $request->status;
            $user->save();
            if ($user) {
                return response()->json('User status update success', 200);
            } else {
                return response()->json(['message' => 'Status update fail'], 400);
            }
        } else {
            return response()->json(["message" => "Record not found"], 400);
        }

    }


    public function edit_profile_update(Request $request)
    {
        // Find the update profile request by ID
        $updateProfile = UpdateProfile::where('user_id', $request->id)->first();
         $updateProfileId = $updateProfile->id;

        if (!$updateProfile) {
            return response()->json([
                "message" => "Profile update request not found.",
            ], 404);
        }

        // Find the user associated with the update profile request
        $user = User::find($updateProfile->user_id);

        if (!$user) {
            return response()->json([
                "message" => "User not found.",
            ], 404);
        }

        // Update the user's profile with the data from the update profile request
        $user->first_name = $updateProfile->first_name;
        $user->last_name = $updateProfile->last_name;
        $user->email = $updateProfile->email;
        $user->phone = $updateProfile->phone;
        $user->buisness_address = $updateProfile->buisness_address;
        $user->buisness_name = $updateProfile->buisness_name;
        $user->image = $updateProfile->image;

        // Save the updated user profile
        $user->save();
       
        $updateProfile->status = 'Active';
        $updateProfile->save();

        return response()->json([
            "message" => "User profile updated successfully.",
            'data' => $user,
        ]);
    }




    public function logoutUser(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $request->user()->token()->revoke();
            return response()->json(['status' => '200', 'message' => 'Successfully logged out']);
        } else {
            return response()->json(['status' => '401', 'message' => 'No user authenticated'], 401);
        }
    }

    public function delete_user($id)
    {

        $user = User::find($id);
        if ($user) {
            $user->delete();
            return response()->json(['status' => '200', 'message' => 'Delete user success']);
        } else {
            return response()->json(['status' => '401', 'message' => 'Record not found']);
        }

    }

    public function all_user(Request $request)
    {
        $query = User::with('user_update')->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $parsonal_data = $query->paginate(8);

        if ($parsonal_data->count() >= 0) {
            return response()->json($parsonal_data);
        } else {
            return response()->json(["message" => "Record not found"], 400);
        }

    }


    public function admin_user()
    {
        $user = User::where('user_type', 'ADMIN')->orWhere('user_type','SUPER ADMIN')->get();
        if ($user) {

            return response()->json(['status' => '200', 'data' => $user]);
        } else {
            return response()->json(['status' => '401', 'message' => 'Record not found']);
        }

    }



}
