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
use validate;
use Illuminate\Support\Facades\Storage;
use App\Models\Parsonal;
use App\Models\BuisnessInfo;
use App\Models\Appoinment;
use App\Models\Tier;
use App\Models\Notification;
use App\Notifications\ProfileUpdateNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class UserController extends Controller
{

    public function adminUserDelete(Request $request)
    {
        $auth_user = Auth::user();

        // Check if the authenticated user is a SUPER-ADMIN
        if ($auth_user->user_type !== 'SUPER-ADMIN') {
            return response()->json(['status' => 403, 'message' => 'Unauthorized action.'], 403);
        }

        // Validate the request to ensure 'user_id' is provided
        $request->validate([
            'user_id' => 'required|integer|exists:users,id', // Check if user_id is provided and exists
        ]);
        // Find the user to delete
        $userToDelete = User::where('id', $request->user_id)
            ->whereIn('user_type', ['ADMIN', 'USER'])
            ->first();

        // Check if the user exists and can be deleted
        if ($userToDelete) {
            $userToDelete->delete();
            return response()->json(['status' => 200, 'message' => 'User deleted successfully.'], 200);
        } else {
            return response()->json(['status' => 404, 'message' => 'User not found or cannot be deleted.'], 404);
        }
    }


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
            return response()->json(['status'=>200, 'message' => 'Please check your email we sent you a code.']);
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
            $token = $user->createToken('example')->plainTextToken; // Use `plainTextToken` for Sanctum
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
    $totalNotification = Notification::where('read_at', null)->count();
    $user = Auth::user();
    if ($user) {
        $userEmail = $user->email;
        $IntackInfo = Parsonal::where('email', $userEmail)->first();
        $parsonalId = $IntackInfo ? $IntackInfo->id : null;
        $buisnessData = $parsonalId ? BuisnessInfo::where('parsonal_id', $parsonalId)->first() : null;
        $tierName = $buisnessData ? $buisnessData->tier_service_interrested : null;

        $tierData = $tierName ? Tier::where('tyer_name',$tierName)->get() : null;
        return response()->json([
            'status' => 200,
            'user' => $user,
            'personalInfo' =>$IntackInfo,
            'BisnessInfo' => $buisnessData,
            'Tier' => $tierData,
            'notification'=> $totalNotification
        ]);
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
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found or email already verified'], 404);
        }
        $currentTime = now();
        $lastResentAt = $user->last_otp_sent_at;
        $expirationTime = 5;
        if ($lastResentAt && $lastResentAt->addMinutes($expirationTime)->isFuture()) {
            return response()->json(['message' => 'You can only resend OTP once every ' . $expirationTime . ' minutes'], 400);
        }
        $newOtp = Str::random(6);
        Mail::to($user->email)->send(new SendOtp($newOtp));
        $user->update(['otp' => $newOtp]);
        $user->update(['last_otp_sent_at' => $currentTime]);
        return response()->json(['message' => 'OTP resent successfully']);
    }
    public function post_update_profile(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $existingRequest = UpdateProfile::where('user_id', $user->id)->first();
            $newProfileUpdate = new UpdateProfile();
            $newProfileUpdate->user_id = $user->id;
            $newProfileUpdate->first_name = $request->first_name ?: $user->first_name;
            $newProfileUpdate->last_name = $request->last_name ?: $user->last_name;
            $newProfileUpdate->email = $request->email ?: $user->email;
            $newProfileUpdate->phone = $request->phone ?: $user->phone;
            $newProfileUpdate->buisness_address = $request->buisness_address ?: $user->buisness_address;
            $newProfileUpdate->buisness_name = $request->buisnes_name ?: $user->buisness_name;
            $newProfileUpdate->save();

            // Notify admins if user type is USER
            if ($user->user_type === 'USER') {
                $admins = User::whereIn('user_type', ['ADMIN', 'SUPER-ADMIN'])->get();
                foreach ($admins as $admin) {
                    $admin->notify(new ProfileUpdateNotification($newProfileUpdate));
                }
            }
            return response()->json([
                'status' => 200,
                'message' => "Profile updated successfully, waiting for admin approval.",
                'data' => $newProfileUpdate,
            ]);
        }
        return response()->json(["message" => "You are not authorized!"], 401);
    }
    public function profile_image_update(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $auth_user = Auth::user();

        if ($auth_user->image) {
            $this->deleteExistingImage($auth_user->image);
        }
        if ($request->hasFile('image')) {
            $auth_user->image = $this->uploadImage($request->file('image'), 'profile_images');
            $auth_user->save();
        }

        return response()->json([
            'status' => 200,
            'message' => 'Profile image updated successfully',
            'profile_image' => asset('storage/' . $auth_user->image),
        ]);
    }
    private function deleteExistingImage($imagePath)
    {
        if ($imagePath && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }
    private function uploadImage($file, $directory)
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/' . $directory, $filename);
        return $directory . '/' . $filename;
    }
    public function profile_image_get()
    {
        $auth_user = Auth::user();

        // Check if the user has an image
        if ($auth_user->image) {
            $image_url = ('storage/' . $auth_user->image); // Construct the image URL
        } else {
            $image_url = ('images/default-profile.png'); // Use a default image if no profile image is set
        }

        // Return the image URL in the response
        return response()->json([
            'status' => 200,
            'message' => 'Profile image retrieved successfully',
            'profile_image' => $image_url,
        ]);
    }


    public function update_profile_all_user()
    {
        // Retrieve users with user_type 'USER'
        $users = User::whereIn('user_type', ['USER'])->get();

        // Retrieve all update profiles
        $updateProfiles = UpdateProfile::all()->keyBy('user_id');

        // Attach update profiles to users
        foreach ($users as $user) {
            $user->updateProfiles = $updateProfiles->get($user->id, []);
        }

        return response()->json([
            'status' => '200',
            'data' => $users,
        ]);
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
        // Ensure the user is authenticated
        if (Auth::check()) {
            // Revoke the current user's token
            $request->user()->currentAccessToken()->delete();

            return response()->json(['status' => '200', 'message' => 'Successfully logged out']);
        } else {
            return response()->json(['status' => '401', 'message' => 'No user authenticated'], 401);
        }
    }
    // public function delete_user($id)
    // {

    //     $user = User::find($id);
    //     $parsonalEmail = $user->email;
    //     $personal = Parsonal::where('email', $parsonalEmail)->with('removeBuisness','removeAppoinment')->get();
    //     if ($personal) {
    //         $personal->delete();
    //         $user->delete();
    //         return response()->json(['status' => '200', 'message' => 'Delete user success']);
    //     }

    //     $user->delete();
    //     return response()->json(['status' => '200', 'message' => 'Delete user success']);
    // }

    public function delete_user($id)
    {
        // Find the user by ID
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => '404', 'message' => 'User not found']);
        }

        // Find the associated personal record by email
        $parsonalEmail = $user->email;
        $personal = Parsonal::where('email', $parsonalEmail)->first();

        if ($personal) {
            try {
                // Remove related business and appointments if necessary
                $personal->removeBuisness()->delete();
                $personal->removeAppoinment()->delete();
                $personal->delete();
            } catch (\Exception $e) {
                return response()->json(['status' => '500', 'message' => 'Error deleting personal records', 'error' => $e->getMessage()]);
            }
        }

        // Delete the user
        try {
            $user->delete();
        } catch (\Exception $e) {
            return response()->json(['status' => '500', 'message' => 'Error deleting user', 'error' => $e->getMessage()]);
        }

        return response()->json(['status' => '200', 'message' => 'Delete user success']);
    }


    public function allCreateUser(Request $request)
    {
        $query = User::where('user_type','user')->orderBy('id', 'desc');

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




public function all_user(Request $request)
{

    $users = User::with('user_update')->orderBy('id', 'desc')->get(); // Use get() instead of all()
    $results = [];

    foreach ($users as $user) {

        $personalInfo = Parsonal::where('email', $user->email)->first();
        // return $personalInfo;

        if ($personalInfo) {
            // Get the personal ID
            $personalId = $personalInfo->id;

            // Get the business data based on the personal ID
            $businessData = BuisnessInfo::where('parsonal_id', $personalId)->first();

            // Get the tier data based on the tier ID from business data
            $tierData = null;
            if ($businessData) {
                $tierId = $businessData->tier_service_interrested;
                $tierData = Tier::find($tierId);
            }

            // Add data to results array
            $results[] = [
                'user' => $user,
                'personal_info' => $personalInfo,
                'business_data' => $businessData,
                'tier_data' => $tierData,
            ];
        }
    }

    // Convert results to a collection
    $resultsCollection = collect($results);

    // Apply search filter if provided
    if ($request->filled('search')) {
        $search = $request->search;
        $resultsCollection = $resultsCollection->filter(function ($item) use ($search) {
            return str_contains(strtolower($item['user']->first_name), strtolower($search)) ||
                   str_contains(strtolower($item['user']->last_name), strtolower($search)) ||
                   str_contains(strtolower($item['user']->email), strtolower($search));
        });
    }

    // Paginate the results
    $currentPage = $request->input('page', 1); // Get the current page number
    $perPage = 8; // Number of results per page
    $currentPageResults = $resultsCollection->forPage($currentPage, $perPage);
    $total = $resultsCollection->count(); // Total number of results

    $paginatedResults = new LengthAwarePaginator(
        $currentPageResults, // Current page results
        $total, // Total number of results
        $perPage, // Number of results per page
        $currentPage, // Current page
        ['path' => Paginator::resolveCurrentPath()] // Base path for pagination links
    );

    // Return the response
    return response()->json($paginatedResults);
}

    // Admin update client type //

    public function updatClientType(Request $request, $id)
    {

        $updateClient = BuisnessInfo::find($id);
        $updateClient->client_type = $request->client_type;
        $updateClient->tier_service_interrested = $request->tier_service_interrested;
        $updateClient->save();
        if(!$updateClient){
            return response()->json(['status'=>200, 'message'=>'update faile']);
        }

        return response()->json(['status'=>200, 'message'=>'update success']);
    }




    public function admin_user()
    {
        $user = User::where('user_type', 'ADMIN')->get();
        if ($user) {

            return response()->json(['status' => '200', 'data' => $user]);
        } else {
            return response()->json(['status' => '401', 'message' => 'Record not found']);
        }

    }




    public function adminUpdate(Request $request, $id) {
        // Validate incoming request
        // $request->validate([
        //     'id' => 'required|exists:users,id',
        //     'first_name' => 'nullable|string|max:255',
        //     'last_name' => 'nullable|string|max:255',
        //     'email' => 'nullable|email|max:255',
        //     'phone' => 'nullable|string|max:15',
        //     'user_type' => 'nullable|string|in:USER,ADMIN,SUPER ADMIN',
        //     'buisness_name' => 'nullable|string|max:255',
        //     'buisness_address' => 'nullable|string|max:255',
        //     'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        // ]);

        // Find the user by ID
        $updateAdmin = User::find($id);

        if (!$updateAdmin) {
            return response()->json(['status' => 404, 'message' => 'User not found'], 404);
        }

        // Update user details with new values or keep the existing ones
        $updateAdmin->first_name = $request->input('first_name', $updateAdmin->first_name);
        $updateAdmin->last_name = $request->input('last_name', $updateAdmin->last_name);
        $updateAdmin->email = $request->input('email', $updateAdmin->email);
        $updateAdmin->phone = $request->input('phone', $updateAdmin->phone);
        $updateAdmin->user_type = $request->input('user_type', $updateAdmin->user_type);
        $updateAdmin->buisness_name = $request->input('buisness_name', $updateAdmin->buisness_name);
        $updateAdmin->buisness_address = $request->input('buisness_address', $updateAdmin->buisness_address);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($updateAdmin->image && Storage::exists(str_replace('/storage/', 'public/', $updateAdmin->image))) {
                Storage::delete(str_replace('/storage/', 'public/', $updateAdmin->image));
            }

            // Store the new image
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('public/image', $fileName);

            // Update the image URL
            $updateAdmin->image = Storage::url('image/' . $fileName);
        }

        // Save the updated user details
        $updateAdmin->save();

        // Return a successful response
        return response()->json(['status' => 200, 'data' => $updateAdmin]);
    }




    public function updateUser($id)
    {
        $user = User::find($id);
        if (empty($user)) {
            return response()->json(['message'=> 'User does not exist'],404);
        }
        if ($user->another_status == 'enable') {
            $user->another_status = 'disable';
            $user->save();
            return response()->json(['message'=> 'User is disable successfully'],200);
        }
        elseif ($user->another_status == 'disable') {
            $user->another_status = 'enable';
            $user->save();
            return response()->json(['message'=> 'User is enable successfully'],200);
        }
}

}
