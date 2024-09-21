<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Price;
use App\Models\Tier;
use App\Http\Requests\PricingRequest;
class PricingController extends Controller
{
    public function index(Request $request)
    {
        $query = Tier::with('price');

        if ($request->filled('pricing_type')) {
            $query->whereHas('price', function($q) use ($request) {
                $q->where('pricing_type', 'like', "%{$request->pricing_type}%");
            });
        }

        // Execute the query to get results
        $tiers = $query->get();

        // Decode the service attribute for each tier's price
        foreach($tiers as $tier) {
            foreach($tier->price as $price) {
                $price->service = json_decode($price->service);
            }
        }

        return response()->json(['status' => 200, 'data' => $tiers]);
    }


    public function store(PricingRequest $request)
    {
        $addTiear = new Tier();
        $addTiear->tyer_name = $request->tyer_name;
        $addTiear->save();

        $addPriceing = new Price();
        $addPriceing->tier_id = $addTiear->id;
        $addPriceing->price_1 = $request->price_1;
        $addPriceing->price_2 = $request->price_2;
        $addPriceing->pricing_type = $request->pricing_type;
        $addPriceing->duration = $request->duration;
        $addPriceing->service = json_encode($request->service);
        $addPriceing->save();
        return response()->json(['status'=> 200,'data'=> $addPriceing]);
        if(!$addPriceing){
            return response()->json(['status'=> 404,'message'=> 'Faile to pricing add']);
        }

    }

    public function update(Request $request, $id )
    {
        $addTiear =  Tier::find($request->id);
        $addTiear->tyer_name = $request->tyer_name ? : $addTiear->tyer_name;
        $addTiear->save();

        $Priceing =  Price::find($request->id);
        $Priceing = Price::where('tier_id',$request->id)->first();
        $Priceing->price_1 = $request->price_1 ? : $Priceing->price_1;
        $Priceing->price_2 = $request->price_2 ? : $Priceing->price_2;
        $Priceing->pricing_type = $request->pricing_type ? : $Priceing->pricing_type;
        $Priceing->duration = $request->duration? : $Priceing->duration;
        $Priceing->service = json_encode($request->service);
        $Priceing->save();
        return response()->json(['status'=> 200,'data'=> $Priceing]);
        if(!$Priceing){
            return response()->json(['status'=> 404,'message'=> 'Faile to pricing add']);
        }

    }

}
