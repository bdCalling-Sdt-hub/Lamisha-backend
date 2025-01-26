<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tier;
use App\Models\BuisnessInfo;
use Illuminate\Support\Facades\Storage;
use App\Models\Parsonal;
use App\Models\ClientDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Str;

class TierController extends Controller
{
    public function updateTier(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tiers.*.id' => 'required|exists:tiers,id',

            'tiers.*.protocol_image' => 'nullable|array',
            'tiers.*.protocol_image.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:5120',

            'tiers.*.standing_image' => 'nullable|array',
            'tiers.*.standing_image.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:5120',

            'tiers.*.policy_image' => 'nullable|array',
            'tiers.*.policy_image.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:5120',

            'tiers.*.constant_image' => 'nullable|array',
            'tiers.*.constant_image.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


    DB::beginTransaction();

    try {
        foreach ($request->tiers as $tierData) {
            $updateTier = Tier::find($tierData['id']);
            $imageFields = ['protocol_image', 'standing_image', 'policy_image', 'constant_image'];

            foreach ($imageFields as $imageField) {
                if (isset($tierData[$imageField])) {
                    $files = is_array($tierData[$imageField]) ? $tierData[$imageField] : [$tierData[$imageField]];
                    if ($updateTier->$imageField) {
                        $existingFiles = json_decode($updateTier->$imageField, true);
                        if (is_array($existingFiles)) {
                            foreach ($existingFiles as $existingFile) {
                                $existingFileName = basename($existingFile);
                                if (Storage::exists('public/image/' . $existingFileName)) {
                                    Storage::delete('public/image/' . $existingFileName);
                                }
                                if (Storage::exists('public/pdf/' . $existingFileName)) {
                                    Storage::delete('public/pdf/' . $existingFileName);
                                }
                            }
                        }
                    }
                    $fileUrls = [];
                    foreach ($files as $file) {
                        if ($file->isValid()) {
                            $fileExtension = $file->getClientOriginalExtension();
                            $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
                            if (in_array($fileExtension, ['jpeg', 'png', 'jpg', 'gif', 'svg'])) {
                                $file->storeAs('public/image', $fileName);
                                $fileUrls[] = '/storage/image/' . $fileName;
                            } elseif ($fileExtension === 'pdf') {
                                $file->storeAs('public/pdf', $fileName);
                                $fileUrls[] = '/storage/pdf/' . $fileName;
                            }
                        }
                    }
                    $updateTier->$imageField = json_encode($fileUrls);
                }
            }
            $updateTier->save();
        }
        DB::commit();
        return response()->json(['message' => 'Tiers updated successfully!'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Tier update error: ', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred while updating tiers.'], 500);
        }

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
        $authUser = auth()->user();
        $userEmail =  $authUser->email;

        if (!$userEmail) {
            return response()->json(['status' => 401, 'message' => 'Unauthorized user'], 401);
        }
        $parsonalInfo = Parsonal::where('email', $userEmail)->first();
        if (!$parsonalInfo) {
            return response()->json(['status' => 400, 'message' => 'Please fill out your intake information'], 400);
        }
        $buisnessInfo = BuisnessInfo::where('parsonal_id', $parsonalInfo->id)->first();
        if (!$buisnessInfo || !$buisnessInfo->tier_service_interrested) {
            return response()->json(['status' => 400, 'message' => 'Please fill out your business information'], 400);
        }
        $tierId = $buisnessInfo->tier_service_interrested;
        $tiers = Tier::where('tyer_name', '<=', $tierId)->orderBy('id', 'asc')->get();
        if (!$tiers) {
            return response()->json(['status' => 400, 'message' => 'Tier information not found'], 400);
        }
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
            'document_status' => $clientDocument ??null,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'An unexpected error occurred',
            'error' => $e->getMessage()
        ], 500);
    }
}


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
