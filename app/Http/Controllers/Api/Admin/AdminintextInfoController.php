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
        if ($request->filled('first_name')) {
            $query->where('first_name', 'like', "%{$request->first_name}%");
        }
        if ($request->filled('last_name')) {
            $query->where('last_name', 'like', "%{$request->last_name}%");
        }
        if ($request->filled('email')) {
            $query->where('email', 'like', "%{$request->email}%");
        }

        $parsonal_data = $query->paginate(8);

        if ($parsonal_data) {
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
                return response()->json('Parsonal status update success', 200);
            } else {
                return response()->json(['message' => 'Status update fail'], 400);
            }
        } else {
            return response()->json(["message" => "Record not found"], 400);
        }

    }
    public function update_buisness_status(Request $request)
    {
        $buisness_data = BuisnessInfo::find($request->id);
        if ($buisness_data) {
            $buisness_data->status = $request->status;
            $buisness_data->save();
            if ($buisness_data) {
                return response()->json('Parsonal status update success', 200);
            } else {
                return response()->json(['message' => 'Status update fail'], 400);
            }
        } else {
            return response()->json(["message" => "Record not found"], 400);
        }

    }

    public function update_appoinment_status(Request $request)
    {
        $appoinment_data = Appoinment::find($request->id);
        if ($appoinment_data) {
            $appoinment_data->status = $request->status;
            $appoinment_data->save();
            if ($appoinment_data) {
                return response()->json('Parsonal status update success', 200);
            } else {
                return response()->json(['message' => 'Status update fail'], 400);
            }
        } else {
            return response()->json(["message" => "Record not found"], 400);
        }

    }
}
