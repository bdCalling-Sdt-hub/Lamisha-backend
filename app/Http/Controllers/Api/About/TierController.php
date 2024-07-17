<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tier;
use Illuminate\Support\Facades\Storage;
class TierController extends Controller
{

    public function storeTier(Request $request)
    {
        $store = new Tier();
        
    
        if ($request->hasFile('tier1')) {
            $tier1Paths = [];
            foreach ($request->file('tier1') as $file) {
                $path = $file->store('tiers/tier1');
                $tier1Paths[] = $path;
            }
            $store->tier1 = json_encode($tier1Paths);
        }
    
        if ($request->hasFile('tier2')) {
            $tier2Paths = [];
            foreach ($request->file('tier2') as $file) {
                $path = $file->store('tiers/tier2');
                $tier2Paths[] = $path;
            }
            $store->tier2 = json_encode($tier2Paths);
        }
    
        if ($request->hasFile('tier3')) {
            $tier3Paths = [];
            foreach ($request->file('tier3') as $file) {
                $path = $file->store('tiers/tier3');
                $tier3Paths[] = $path;
            }
            $store->tier3 = json_encode($tier3Paths);
        }
    
        if ($request->hasFile('tier4')) {
            $tier4Paths = [];
            foreach ($request->file('tier4') as $file) {
                $path = $file->store('tiers/tier4');
                $tier4Paths[] = $path;
            }
            $store->tier4 = json_encode($tier4Paths);
        }
        $store->tiers_type = $request->tiers_type;
    
        $store->save();
        if($store){
            return response()->json('insert success');
        }
    }

    public function show_tiear()
    {
        // Fetch all tier data from the Tier model
        $tierData = Tier::all();
        
        if($tierData) {
            // Iterate over each record to decode image paths
            $decodedData = $tierData->map(function($tier) {
                return [
                    'id'=> $tier->id,
                    'tiers_type' => $tier->tiers_type,
                    'tier1' => json_decode($tier->tier1, true),
                    'tier2' => json_decode($tier->tier2, true),
                    'tier3' => json_decode($tier->tier3, true),
                    'tier4' => json_decode($tier->tier4, true),
                ];
            });
    
            return response()->json($decodedData);
        } else {
            return response()->json('Record not found');
        }
    }
    



    public function updateTier(Request $request, $id)
    {
        $tier = Tier::findOrFail($id);
        
        // Update tiers_type
        $tier->tiers_type = $request->input('tiers_type', $tier->tiers_type);
    
        // Helper function to delete existing images
        function deleteExistingImages($paths) {
            foreach ($paths as $path) {
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
            }
        }
    
        // Handle tier files
        foreach (['tier1', 'tier2', 'tier3', 'tier4'] as $tierType) {
            if ($request->hasFile($tierType)) {
                $tierPaths = $tier->$tierType ? json_decode($tier->$tierType, true) : [];
                deleteExistingImages($tierPaths); // Delete existing images
    
                $tierPaths = [];
                foreach ($request->file($tierType) as $file) {
                    $path = $file->store("tiers/{$tierType}");
                    $tierPaths[] = $path;
                }
                $tier->$tierType = json_encode($tierPaths);
            }
        }
    
        $tier->save();
        if($tier){
            return response()->json(['message' => 'Tier updated successfully'], 200);
        }else{
            return response()->json(['message'=> 'Update fiale '],404);
    
        }
    }
    
    

    
}
