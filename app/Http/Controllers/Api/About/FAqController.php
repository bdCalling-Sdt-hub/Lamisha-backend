<?php

namespace App\Http\Controllers\Api\About;
use App\Models\Faq;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FAqController extends Controller
{
    public function faq_index()
    {
        $faq = Faq::all();
        if ($faq) {
            return response()->json(['status'=>'200', 'data'=>$faq]);
        }else{
            return response()->json(['status'=>'401', 'message'=>'Data not found']);
        }
        
    }
    public function show($id)
    {
        $faq = Faq::find($id);
        if ($faq) {
            return response()->json(['status' => 200, 'data' => $faq]);
        } else {
            return response()->json(['status' => 404, 'message' => 'Data not found']);
        }
    }

    // Create a new record
    public function store(Request $request)
    {
        $faq = Faq::create($request->all());
        return response()->json(['status' => 201, 'data' => $faq]);
    }

    // Update an existing record by ID
    public function update(Request $request, $id)
    {
        $faq = Faq::find($id);
        if ($faq) {
            $faq->update($request->all());
            return response()->json(['status' => 200, 'data' => $faq]);
        } else {
            return response()->json(['status' => 404, 'message' => 'Data not found']);
        }
    }

    public function destroy(Request $request, $id)
    {
        $faq = Faq::find($id);
        if ($faq) {
            $faq->delete();
            return response()->json(['status' => 200, 'message' => 'Delete successfully']);
        } else {
            return response()->json(['status' => 404, 'message' => 'Data not found']);
        }
    }
}

