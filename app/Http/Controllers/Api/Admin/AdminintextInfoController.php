<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Parsonal;
use App\Models\BuisnessInfo;
use App\Models\Appoinment;

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
}
