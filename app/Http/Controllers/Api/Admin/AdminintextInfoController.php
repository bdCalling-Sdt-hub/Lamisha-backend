<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Parsonal;
use App\Models\BuisnessInfo;
use App\Models\Appoinment;
use App\Models\Notification;
use App\Notifications\IntakInfoNotification;

class AdminintextInfoController extends Controller
{
    public function intekInof(Request $request)
    {
        $query = Parsonal::orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $parsonal_data = $query->paginate(8);

        if ($parsonal_data->count() > 0) {
            return response()->json($parsonal_data);
        } else {
            return response()->json(["message" => "Record not found"], 400);
        }
    }


    public function singleIntek_info($id)
    {
        $parsonal_data = Parsonal::where('id', $id)->with('buisness', 'appoinment')->first();
        if ($parsonal_data) {
            return response()->json($parsonal_data);
        } else {
            return response()->json(["message" => "Record not found"], 400);
        }
    }

    public function update_parsonal_status(Request $request)
    {
        $parsonal_data = Parsonal::find($request->id);
        if ($parsonal_data) {
            $parsonal_data->status = $request->status;
            $parsonal_data->save();
            if ($parsonal_data) {
                return response()->json(['status'=>200, 'Parsonal status update success', 200]);
            } else {
                return response()->json(['status'=>400,'message' => 'Status update fail'], 400);
            }
        } else {
            return response()->json(['status'=>400,"message" => "Record not found"], 400);
        }

    }
    public function update_buisness_status(Request $request)
    {
        $buisness_data = BuisnessInfo::find($request->id);
        if ($buisness_data) {
            $buisness_data->status = $request->status;
            $buisness_data->save();
            if ($buisness_data) {
                return response()->json(['status'=>200,'Parsonal status update success', 200]);
            } else {
                return response()->json(['status'=>400,'message' => 'Status update fail'], 400);
            }
        } else {
            return response()->json(['status'=>400,"message" => "Record not found"], 400);
        }

    }

    public function update_appoinment_status(Request $request)
    {
        $appoinment_data = Appoinment::find($request->id);
        if ($appoinment_data) {
            $appoinment_data->status = $request->status;
            $appoinment_data->save();
            if ($appoinment_data) {
                return response()->json(['status'=>200,'Parsonal status update success', 200]);
            } else {
                return response()->json(['status'=>400,'message' => 'Status update fail'], 400);
            }
        } else {
            return response()->json(['status'=>200, "message" => "Record not found"], 400);
        }
    }



    public function getUserNotifications(Request $request)
    {
        // Fetch paginated notifications
        $notifications = Notification::orderByRaw('read_at IS NULL DESC')
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(10);

        // Decode the 'data' field for each notification
        $allNotifications = $notifications->map(function ($notification) {
            $data = json_decode($notification->data, true); // Decode the JSON data

            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'data' => $data, // Include decoded data
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at,
                'updated_at' => $notification->updated_at,
            ];
        });

        // Return notifications as JSON response with pagination metadata
        return response()->json([
            'data' => $allNotifications, // Actual notifications
            'current_page' => $notifications->currentPage(),
            'total_pages' => $notifications->lastPage(),
            'total_items' => $notifications->total(),
            'per_page' => $notifications->perPage(),
        ]);
    }


    public function updateNotification($id)
    {
         $date = date('Y-m-d');
        $updateNotification = Notification::find($id);
        $updateNotification->read_at = $date;
        $updateNotification->save();
        if(!$updateNotification){
            return response()->json( 'Update Notification faile');
        }
        return response()->json( 'Update Notification successfull');
    }
}


