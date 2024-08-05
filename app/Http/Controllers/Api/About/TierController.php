<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tier;
use Illuminate\Support\Facades\Storage;

class TierController extends Controller
{

    public function updateTier(Request $request)
    {
        // Check if the image is present in the request
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
        } else {
            return response()->json(['status' => 400, 'message' => 'Image not provided']);
        }

        // Check if cq is an array
        if (!is_array($request->cq)) {
            return response()->json(['status' => 400, 'message' => 'cq is not an array']);
        }

        foreach ($request->cq as $key => $cq) {
            // Check if the protocols array exists and has the current key
            if (!isset($request->protocols[$key])) {
                continue; // Skip this iteration if the protocols array doesn't have the current key
            }

            $chk = Tier::where('protocols', $imageName)->first();

            if ($chk) {
                $data = Tier::find($chk->id);
                $data->protocols = $request->protocols[$key];
                $data->save();
            }
        }

        return response()->json(['status' => 200, 'message' => 'Data inserted successfully']);
    }





    // $protocols = $request->file('protocol');
// $standingOrder = $request->input('standingOrder');
// $policies = $request->input('policies');
// $consents = $request->input('consents');

    // if ($protocols) {
//     foreach ($protocols as $protocol) {
//         $path = $protocol->store('protocols');
//         // Update your database with the path
//     }
// }

    // Handle other form fields similarly

    //return response()->json(['status' => 'success', 'message' => 'Update successful']);



    // $standing = $request->standing;
    // $constant = $request->constant; 
    // $policis = $request->policis; 

    // $student_name = $request->student_name;
    // $present= $request->present;




    //     $store = new Tier();


    //     if ($request->hasFile('protocol')) {
    //         $tier1Paths = [];
    //         foreach ($request->file('protocol') as $file) {
    //             $path = $file->store('tiers/tier1');
    //             $tier1Paths[] = $path;
    //         }
    //         $store->tier1 = json_encode($tier1Paths);
    //     }

    //     if ($request->hasFile('tier2')) {
    //         $tier2Paths = [];
    //         foreach ($request->file('tier2') as $file) {
    //             $path = $file->store('tiers/tier2');
    //             $tier2Paths[] = $path;
    //         }
    //         $store->tier2 = json_encode($tier2Paths);
    //     }

    //     if ($request->hasFile('tier3')) {
    //         $tier3Paths = [];
    //         foreach ($request->file('tier3') as $file) {
    //             $path = $file->store('tiers/tier3');
    //             $tier3Paths[] = $path;
    //         }
    //         $store->tier3 = json_encode($tier3Paths);
    //     }

    //     if ($request->hasFile('tier4')) {
    //         $tier4Paths = [];
    //         foreach ($request->file('tier4') as $file) {
    //             $path = $file->store('tiers/tier4');
    //             $tier4Paths[] = $path;
    //         }
    //         $store->tier4 = json_encode($tier4Paths);
    //     }
    //     $store->tiers_type = $request->tiers_type;

    //     $store->save();
    //     if(!$store){
    //         return response()->json(['status'=>400, 'message'=>'Failed to insert data'],400);
    //     }
    //     return response()->json(['status'=>200, 'message'=>'insert success'],200);
    // }

    public function show_tiear()
    {
        $tierData = Tier::orderBy('id', 'desc')->get();

        if (!$tierData) {
            return response()->json('Record not found');
        }
        return response()->json(['status' => 200, 'data' => $tierData]);
    }




    // public function updateTier(Request $request, $id)
    // {
    //     $tier = Tier::findOrFail($id);

    //     // Update tiers_type
    //     $tier->tiers_type = $request->input('tiers_type', $tier->tiers_type);

    //     // Helper function to delete existing images
    //     function deleteExistingImages($paths) {
    //         foreach ($paths as $path) {
    //             if (Storage::exists($path)) {
    //                 Storage::delete($path);
    //             }
    //         }
    //     }

    //     // Handle tier files
    //     foreach (['tier1', 'tier2', 'tier3', 'tier4'] as $tierType) {
    //         if ($request->hasFile($tierType)) {
    //             $tierPaths = $tier->$tierType ? json_decode($tier->$tierType, true) : [];
    //             deleteExistingImages($tierPaths); // Delete existing images

    //             $tierPaths = [];
    //             foreach ($request->file($tierType) as $file) {
    //                 $path = $file->store("tiers/{$tierType}");
    //                 $tierPaths[] = $path;
    //             }
    //             $tier->$tierType = json_encode($tierPaths);
    //         }
    //     }

    //     $tier->save();
    //     if($tier){
    //         return response()->json(['message' => 'Tier updated successfully'], 200);
    //     }else{
    //         return response()->json(['message'=> 'Update fiale '],404);

    //     }





}
