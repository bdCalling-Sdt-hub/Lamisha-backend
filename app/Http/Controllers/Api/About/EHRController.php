<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EHR;

class EHRController extends Controller
{
    public function EHR_index()
    {
        $EHR = EHR::all();
        if ($EHR) {
            return response()->json(['status'=>'200', 'data'=>$EHR]);
        }else{
            return response()->json(['status'=>'401', 'message'=>'Data not found']);
        }
        
    }
    public function show($id)
    {
        $EHR = EHR::find($id);
        if ($EHR) {
            return response()->json(['status' => 200, 'data' => $EHR]);
        } else {
            return response()->json(['status' => 404, 'message' => 'Data not found']);
        }
    }

    // Create a new record
    public function store(Request $request)
    {
        $EHR = EHR::create($request->all());
        return response()->json(['status' => 201, 'data' => $EHR]);
    }

    // Update an existing record by ID
    public function update(Request $request, $id)
    {
        $EHR = EHR::find($id);
        if ($EHR) {
            $EHR->update($request->all());
            return response()->json(['status' => 200, 'data' => $EHR]);
        } else {
            return response()->json(['status' => 404, 'message' => 'Data not found']);
        }
    }

    public function destroy(Request $request, $id)
    {
        $EHR = EHR::find($id);
        if ($EHR) {
            $EHR->delete();
            return response()->json(['status' => 200, 'message' => 'Delete successfully']);
        } else {
            return response()->json(['status' => 404, 'message' => 'Data not found']);
        }
    }
}
