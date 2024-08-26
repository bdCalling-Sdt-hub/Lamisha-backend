<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tier;
use App\Models\BuisnessInfo;
use Illuminate\Support\Facades\Storage;
use App\Models\Parsonal;
use App\Models\ClientDocument;
class TierController extends Controller
{


    // public function updateTier(Request $request)
    //   {        
    //       // Validate the request data
    //       $request->validate([
    //           'tiers.*.id' => 'required|exists:tiers,id',
    //           'tiers.*.protocol_image' => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif,svg|max:2048',
    //           'tiers.*.standing_image' => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif,svg|max:2048',
    //           'tiers.*.policy_image' => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif,svg|max:2048',
    //           'tiers.*.constant_image' => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif,svg|max:2048'
    //       ]);

    //       foreach ($request->tiers as $tierData) {

    //           $updateTier = Tier::find($tierData['id']);

    //           // Define the image fields to be updated
    //           $imageFields = ['protocol_image', 'standing_image', 'policy_image', 'constant_image'];

    //           foreach ($imageFields as $imageField) {
    //               if (isset($tierData[$imageField]) && $tierData[$imageField]->isValid()) {
    //                   // Check if there's an existing image
    //                   if ($updateTier->$imageField) {
    //                       // Extract the file name from the URL
    //                       $existingFileName = basename($updateTier->$imageField);

    //                       // Delete the existing image file
    //                       Storage::delete('public/image/' . $existingFileName);
    //                   }

    //                   // Store the new image file
    //                   $file = $tierData[$imageField];
    //                   $timeStamp = time(); // Current timestamp
    //                   $fileName = $timeStamp . '.' . $file->getClientOriginalExtension();
    //                   $file->storeAs('public/image', $fileName);
    //                   $fileUrl = '/storage/image/' . $fileName;
    //                   $updateTier->$imageField = $fileUrl;
    //               }
    //           }

    //           $updateTier->save();
    //       }

    //       return response()->json(['status'=>200, 'message'=>'Update successfull', ]);
    //   }

    public function updateTier(Request $request)
    {
        // Validate the request data
        $request->validate([
            'tiers.*.id' => 'required|exists:tiers,id',
            'tiers.*.protocol_image.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif,svg|max:2048',
            'tiers.*.standing_image.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif,svg|max:2048',
            'tiers.*.policy_image.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif,svg|max:2048',
            'tiers.*.constant_image.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        foreach ($request->tiers as $tierData) {
            $updateTier = Tier::find($tierData['id']);
    
            // Define the image fields to be updated
            $imageFields = [
                'protocol_image',
                'standing_image',
                'policy_image',
                'constant_image'
            ];
    
            foreach ($imageFields as $imageField) {
                if (isset($tierData[$imageField]) && count($tierData[$imageField]) > 0) {
                    // Delete existing images for this field
                    if ($updateTier->$imageField) {
                        $existingFiles = json_decode($updateTier->$imageField, true);
                        foreach ($existingFiles as $existingFile) {
                            $existingFileName = basename($existingFile);
                            Storage::delete('public/image/' . $existingFileName);
                        }
                    }
    
                    // Store the new image files
                    $fileUrls = [];
                    foreach ($tierData[$imageField] as $file) {
                        if ($file->isValid()) {
                            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                            $file->storeAs('public/image', $fileName);
                            $fileUrls[] = '/storage/image/' . $fileName;
                        }
                    }
    
                    // Store the array of URLs as a JSON string in the database
                    $updateTier->$imageField = json_encode($fileUrls);
                }
            }
    
            $updateTier->save();
        }
    
        return response()->json(['status' => 200, 'message' => 'Update successful']);
    }
      

    


    public function show_tiear()
    {     
       return $tierData = Tier::get();

        if (!$tierData) {
            return response()->json('Record not found');
        }
        return response()->json(['status' => 200, 'data' => $tierData]);
    }

//     public function client_tier()
// {
//     try {
//         $authUser = auth()->user();
//         $userEmail =  $authUser->email;
//         if (!$userEmail) {
//             return response()->json(['status' => 401, 'message' => 'Unauthorized user'], 401);
//         }
//         $clientDocuemnt = ClientDocument::where('user_id', $authUser)->first();
//         $parsonalInfo = Parsonal::where('email', $userEmail)->first();
//         if (!$parsonalInfo) {
//             return response()->json(['status' => 400, 'message' => 'Please fill out your intake information'], 400);
//         }
//         $buisnessInfo = BuisnessInfo::where('parsonal_id', $parsonalInfo->id)->first();
//         if (!$buisnessInfo || !$buisnessInfo->tier_service_interrested) {
//             return response()->json(['status' => 400, 'message' => 'Please fill out your business information'], 400);
//         }
//         $tierId = $buisnessInfo->tier_service_interrested;
//         $tiers = Tier::where('id', '<=', $tierId)->orderBy('id', 'desc')->get();
      
//         if ($tiers->isEmpty()) {
//             return response()->json(['status' => 400, 'message' => 'Tier information not found'], 400);
//         }
//         return response()->json(['status' => 200, 'data' => $tiers, 'client_document'=>$clientDocuemnt]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => 500,
//             'message' => 'An unexpected error occurred',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// }

public function client_tier()
{
    try {
        $authUser = auth()->user();
        $userEmail =  $authUser->email;
        if (!$userEmail) {
            return response()->json(['status' => 401, 'message' => 'Unauthorized user'], 401);
        }

        $clientDocument = ClientDocument::where('user_id', $authUser->id)->first()->status;
        $parsonalInfo = Parsonal::where('email', $userEmail)->first();
        
        if (!$parsonalInfo) {
            return response()->json(['status' => 400, 'message' => 'Please fill out your intake information'], 400);
        }

        $buisnessInfo = BuisnessInfo::where('parsonal_id', $parsonalInfo->id)->first();
        if (!$buisnessInfo || !$buisnessInfo->tier_service_interrested) {
            return response()->json(['status' => 400, 'message' => 'Please fill out your business information'], 400);
        }
        $tierId = $buisnessInfo->tier_service_interrested;

    //    return $tierId = $buisnessInfo->tier_service_interested;
        $tiers = Tier::where('id', '<=', $tierId)->orderBy('id', 'desc')->get();

        if ($tiers->isEmpty()) {
            return response()->json(['status' => 400, 'message' => 'Tier information not found'], 400);
        }

        // Decode image fields
        $tiers = $tiers->map(function($tier) {
            $tier->protocol_image = json_decode($tier->protocol_image);
            $tier->standing_image = json_decode($tier->standing_image);
            $tier->policy_image = json_decode($tier->policy_image);
            $tier->constant_image = json_decode($tier->constant_image);
            return $tier;
        });

        return response()->json([
            'status' => 200,
            'data' => $tiers,
            'document_status' => $clientDocument
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'An unexpected error occurred',
            'error' => $e->getMessage()
        ], 500);
    }
}


    

    // Web tier //

    public function showTiearPricing()
    {
        // Fetch the tiers with related prices
        $tierData = Tier::with('price')->orderBy('id', 'desc')->get();
    
        // Check if any tier data is returned
        if ($tierData->isEmpty()) {
            return response()->json(['message' => 'Record not found'], 404);
        }
    
        // Decode the 'service' field for each price entry
        foreach ($tierData as $tier) {
            foreach ($tier->price as $price) {
                // Decode 'service' if it is a JSON-encoded string
                if (is_string($price->service)) {
                    $price->service = json_decode($price->service, true);
                }
            }
        }
    
        return response()->json(['status' => 200, 'data' => $tierData]);
    }
    

}
