<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Privacy;
class PrivacyPolicyController extends Controller
{
    public function privacy_index()
    {
        $privacy = Privacy::all();
        if ($privacy) {
            return response()->json(['status'=>'200', 'data'=>$privacy]);
        }else{
            return response()->json(['status'=>'401', 'message'=>'Data not found']);
        }
        
    }
    public function show($id)
    {
        $privacy = Privacy::find($id);
        if ($privacy) {
            return response()->json(['status' => 200, 'data' => $privacy]);
        } else {
            return response()->json(['status' => 404, 'message' => 'Data not found']);
        }
    }

    // Create a new record
    public function store(Request $request)
    {
        $privacy = Privacy::create($request->all());
        return response()->json(['status' => 201, 'data' => $privacy]);
    }

    // Update an existing record by ID
    public function update(Request $request, $id)
    {
        $privacy = Privacy::find($id);
        if ($privacy) {
            $privacy->update($request->all());
            return response()->json(['status' => 200, 'data' => $privacy]);
        } else {
            return response()->json(['status' => 404, 'message' => 'Data not found']);
        }
    }
}
