<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Privacy;
class PrivacyPolicyController extends Controller
{
    public function privacy_index()
    {
        $privacy = Privacy::first();
        if ($privacy) {
            return response()->json(['status'=>200, 'data'=>$privacy]);
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
        $id = $request->input('id');
        $about = Privacy::find($id);

        if ($about) {
            // Record exists, so update it
            $about->update($request->all());
            return response()->json(['status' => 200, 'data' => $about, 'message' => 'Data updated successfully']);
        } else {
            // Record does not exist, so create a new one
            $about = Privacy::create($request->all());
            return response()->json(['status' => 201, 'data' => $about, 'message' => 'Data created successfully']);
        }
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
