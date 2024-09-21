<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\stateCvered;
class CoveredController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $state = StateCvered::orderBy('state_name', 'asc')->get();

        return $state;
        if($state){
            return response()->json(['status'=>'200', 'data'=>$state]);
        }else{
            return response()->json(['status'=>'400', 'message'=>'data not found']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $state = new stateCvered();
        $state->state_name = $request->state_name;
        $state->save();
        if($state){
            return response()->json(['status'=>'200', 'data'=>$state]);
        }else{
            return response()->json(['status'=>'400', 'message'=>'date insert faile']);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         $state = stateCvered::find($id);
         $state->delete();
        if($state){
            return response()->json(['status'=>'200', 'message'=>'Delete success fully']);
        }else{
            return response()->json(['status'=>'400', 'message'=>'data not found']);
        }
    }
}
