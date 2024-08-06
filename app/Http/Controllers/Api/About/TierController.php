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



    // public function updateTier(Request $request)
    // {
    //     // Validate the request data
    //     $request->validate([
    //         //'tiers' => 'required|array',
    //         'tiers.*.id' => 'required|exists:tiers,id',
    //         'tiers.*.protocol_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048'
    //     ]);

    //     foreach ($request->tiers as $tierData) {
    //         $updateTier = Tier::find($tierData['id']);

    //         if (isset($tierData['protocol_image']) && $tierData['protocol_image']->isValid()) {
    //             $file = $tierData['protocol_image'];
    //             $timeStamp = time(); // Current timestamp
    //             $fileName = $timeStamp . '.' . $file->getClientOriginalExtension();
    //             $file->storeAs('public/image', $fileName);
    //             $fileUrl = '/storage/image/' . $fileName;
    //             $updateTier->protocol_image = $fileUrl;
    //         }

    //         $updateTier->save();
    //     }

    //     return response()->json(['ok']);
    // }

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

    return response()->json(['status'=>200, 'message'=>'Update successfull']);
}



    public function show_tiear()
    {
        $tierData = Tier::orderBy('id', 'desc')->get();

        if (!$tierData) {
            return response()->json('Record not found');
        }
        return response()->json(['status' => 200, 'data' => $tierData]);
    }

    public function client_tier()
    {
       
      $valid_user = auth()->user()->email;
      if(! $valid_user){
        return response()->json('Unauthrize user');
      }
      $parsonalId = Parsonal::where('email','=', $valid_user)->first()->id;
      if(! $parsonalId){
        return response()->json('Plese filup your inteck information');
      }
       $findTier = BuisnessInfo::where('parsonal_id',$parsonalId)->first()->tier_service_interrested;

       if(! $findTier){
         return response()->json('Plese filup your bisness information');
       }
      $tier = Tier::where('id', $findTier)->first();
      if(! $tier){
        return response()->json('Tier inforam');
      }
      return response()->json(['status'=> 200,'data'=> $tier,]);        
    }

}
