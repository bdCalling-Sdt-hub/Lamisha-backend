<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\About;
use App\Models\TermsConditions;
use App\Models\Faq;
use Illuminate\Support\Facades\Validator;

class AboutController extends Controller
{
    public function about_index()
    {
        $about = About::first();
        if ($about) {
            return response()->json(['status' => '200', 'data' => $about]);
        } else {
            return response()->json(['status' => '401', 'message' => 'Data not found']);
        }

    }
    public function show($id)
    {
        $about = About::find($id);
        if ($about) {
            return response()->json(['status' => 200, 'data' => $about]);
        } else {
            return response()->json(['status' => 404, 'message' => 'Data not found']);
        }
    }

    // Create a new record
    public function store(Request $request)
    {
        $about = About::create($request->all());
        return response()->json(['status' => 201, 'data' => $about]);
    }

    // Update an existing record by ID
    public function update(Request $request, $id)
    {
        $about = About::find($id);
        if ($about) {
            $about->update($request->all());
            return response()->json(['status' => 200, 'data' => $about]);
        } else {
            return response()->json(['status' => 404, 'message' => 'Data not found']);
        }
    }

    public function storeOrUpdate(Request $request)
    {
        $id = $request->input('id');
        $about = About::find($id);

        if ($about) {
            // Record exists, so update it
            $about->update($request->all());
            return response()->json(['status' => 200, 'data' => $about, 'message' => 'Data updated successfully']);
        } else {
            // Record does not exist, so create a new one
            $about = About::create($request->all());
            return response()->json(['status' => 201, 'data' => $about, 'message' => 'Data created successfully']);
        }
    }




}
