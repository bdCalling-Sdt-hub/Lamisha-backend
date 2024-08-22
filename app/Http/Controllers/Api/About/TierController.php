<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tier;
use App\Models\BuisnessInfo;
use Illuminate\Support\Facades\Storage;
use App\Models\Parsonal;
class TierController extends Controller
{


    public function updateTier(Request $request)
      {        
          // Validate the request data
          $request->validate([
              'tiers.*.id' => 'required|exists:tiers,id',
              'tiers.*.protocol_image' => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif,svg|max:2048',
              'tiers.*.standing_image' => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif,svg|max:2048',
              'tiers.*.policy_image' => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif,svg|max:2048',
              'tiers.*.constant_image' => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif,svg|max:2048'
          ]);

          foreach ($request->tiers as $tierData) {

              $updateTier = Tier::find($tierData['id']);

              // Define the image fields to be updated
              $imageFields = ['protocol_image', 'standing_image', 'policy_image', 'constant_image'];

              foreach ($imageFields as $imageField) {
                  if (isset($tierData[$imageField]) && $tierData[$imageField]->isValid()) {
                      // Check if there's an existing image
                      if ($updateTier->$imageField) {
                          // Extract the file name from the URL
                          $existingFileName = basename($updateTier->$imageField);

                          // Delete the existing image file
                          Storage::delete('public/image/' . $existingFileName);
                      }

                      // Store the new image file
                      $file = $tierData[$imageField];
                      $timeStamp = time(); // Current timestamp
                      $fileName = $timeStamp . '.' . $file->getClientOriginalExtension();
                      $file->storeAs('public/image', $fileName);
                      $fileUrl = '/storage/image/' . $fileName;
                      $updateTier->$imageField = $fileUrl;
                  }
              }

              $updateTier->save();
          }

          return response()->json(['status'=>200, 'message'=>'Update successfull', ]);
      }

    public function show_tiear()
    {
     
       return $tierData = Tier::get();

        if (!$tierData) {
            return response()->json('Record not found');
        }
        return response()->json(['status' => 200, 'data' => $tierData]);
    }

    public function client_tier()
{
    try {
        // Get the authenticated user's email
        $userEmail = auth()->user()->email;

        // Check if user is authenticated
        if (!$userEmail) {
            return response()->json(['status' => 401, 'message' => 'Unauthorized user'], 401);
        }

        // Fetch personal information based on the user's email
        $parsonalInfo = Parsonal::where('email', $userEmail)->first();

        // Check if personal information is found
        if (!$parsonalInfo) {
            return response()->json(['status' => 400, 'message' => 'Please fill out your intake information'], 400);
        }

        // Fetch business information based on the personal ID
        $buisnessInfo = BuisnessInfo::where('parsonal_id', $parsonalInfo->id)->first();

        // Check if business information is found and has the tier ID
        if (!$buisnessInfo || !$buisnessInfo->tier_service_interrested) {
            return response()->json(['status' => 400, 'message' => 'Please fill out your business information'], 400);
        }

        // Get the tier ID the user is interested in
        $tierId = $buisnessInfo->tier_service_interrested;

        // Fetch tiers with IDs less than or equal to the specified tier ID
        $tiers = Tier::where('id', '<=', $tierId)->orderBy('id', 'desc')->get();

        // Check if any tier information is found
        if ($tiers->isEmpty()) {
            return response()->json(['status' => 400, 'message' => 'Tier information not found'], 400);
        }

        // Return the list of tiers if all checks pass
        return response()->json(['status' => 200, 'data' => $tiers]);

    } catch (\Exception $e) {
        // Catch any unexpected errors and return a 500 Internal Server Error
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
