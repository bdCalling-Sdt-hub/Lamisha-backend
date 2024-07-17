<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminVendor;
use App\Jobs\SendOrder;
class VendorController extends Controller
{
    public function AdminVendor_index()
    {
        $AdminVendor = AdminVendor::all();
        if ($AdminVendor) {
            return response()->json(['status'=>'200', 'data'=>$AdminVendor]);
        }else{
            return response()->json(['status'=>'401', 'message'=>'Data not found']);
        }
        
    }

    // Create a new record
    public function store(Request $request)
    {
        $AdminVendor = AdminVendor::create($request->all());
        return response()->json(['status' => 201, 'data' => $AdminVendor]);
    }

    // Update an existing record by ID
   
    public function destroy(Request $request, $id)
    {
        $AdminVendor = AdminVendor::find($id);
        if ($AdminVendor) {
            $AdminVendor->delete();
            return response()->json(['status' => 200, 'message' => 'Delete successfully']);
        } else {
            return response()->json(['status' => 404, 'message' => 'Data not found']);
        }
    }

    // ------------------- Client side vendor send mail ----------------------------- //

    public function confirmOrder(Request $request)
    {
        $data = [
            'first_name'=> $request->input('first_name'),
            'last_name'=> $request->input('last_name'),
            'email'=> $request->input('email'),
            'phone'=> $request->input('phone'),
            'shiping_address'=> $request->input('shiping_address'),
            'item_description'=> $request->input('item_description'),
            'item_number'=> $request->input('item_number'),
            'price'=> $request->input('price'), 
            'quantity'=> $request->input('quantity'),
            'vendor'=> $request->input('vendor'),
            'comments'=> $request->input('comments'),
            'print_name'=> $request->input('print_name'),
        ];
        dispatch(new SendOrder($data));
        return response()->json(['status'=> '200', 'message'=> "Order confirm successfull"]);
    }
}
