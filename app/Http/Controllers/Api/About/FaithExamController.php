<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FithExam;
class FaithExamController extends Controller
{
    public function FithExam_index()
    {
        $FithExam = FithExam::all();
        if ($FithExam) {
            return response()->json(['status'=>'200', 'data'=>$FithExam]);
        }else{
            return response()->json(['status'=>'401', 'message'=>'Data not found']);
        }
        
    }
    public function show($id)
    {
        $FithExam = FithExam::find($id);
        if ($FithExam) {
            return response()->json(['status' => 200, 'data' => $FithExam]);
        } else {
            return response()->json(['status' => 404, 'message' => 'Data not found']);
        }
    }

    // Create a new record
    public function store(Request $request)
    {
        $FithExam = FithExam::create($request->all());
        return response()->json(['status' => 201, 'data' => $FithExam]);
    }

    // Update an existing record by ID
    public function update(Request $request, $id)
    {
        $FithExam = FithExam::find($id);
        if ($FithExam) {
            $FithExam->update($request->all());
            return response()->json(['status' => 200, 'data' => $FithExam]);
        } else {
            return response()->json(['status' => 404, 'message' => 'Data not found']);
        }
    }

    public function destroy(Request $request, $id)
    {
        $FithExam = FithExam::find($id);
        if ($FithExam) {
            $FithExam->delete();
            return response()->json(['status' => 200, 'message' => 'Delete successfully']);
        } else {
            return response()->json(['status' => 404, 'message' => 'Data not found']);
        }
    }
}
