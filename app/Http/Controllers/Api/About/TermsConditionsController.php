<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TermsConditions;
class TermsConditionsController extends Controller
{
    public function terms_index()
    {
        $terms = TermsConditions::all();
        if ($terms) {
            return response()->json(['status'=>'200', 'data'=>$terms]);
        }else{
            return response()->json(['status'=>'401', 'message'=>'Data not found']);
        }
        
    }
    public function show($id)
    {
        $terms = TermsConditions::find($id);
        if ($terms) {
            return response()->json(['status' => 200, 'data' => $terms]);
        } else {
            return response()->json(['status' => 404, 'message' => 'Data not found']);
        }
    }

    // Create a new record
    public function store(Request $request)
    {
        $terms = TermsConditions::create($request->all());
        return response()->json(['status' => 201, 'data' => $terms]);
    }

    // Update an existing record by ID
    public function update(Request $request, $id)
    {
        $terms = TermsConditions::find($id);
        if ($terms) {
            $terms->update($request->all());
            return response()->json(['status' => 200, 'data' => $terms]);
        } else {
            return response()->json(['status' => 404, 'message' => 'Data not found']);
        }
    }
}
