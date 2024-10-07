<?php

namespace App\Http\Controllers\Api\About;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminVendor;
use App\Jobs\SendOrder;
use App\Models\QA;
use App\Mail\QaMail;
use Mail;
use auth;
use Illuminate\Support\Facades\Storage;
class VendorController extends Controller
{
    public function AdminVendor_index()
    {
        $AdminVendor = AdminVendor::orderBy("vendor_name","asc")->get();
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

        // Add QA section//
        public function qaIndex()
    {
        $AdminVendor = QA::orderBy('id','desc')->get();
        if ($AdminVendor) {
            return response()->json(['status'=>'200', 'data'=>$AdminVendor]);
        }else{
            return response()->json(['status'=>'401', 'message'=>'Data not found']);
        }

    }


    public function qaStore(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'email' => 'required|email',
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'files.*' => 'file|mimes:jpeg,png,pdf,docx|max:2048',
    ]);

    // Initialize an array to hold file paths
    $filePaths = [];

    // Check if files are present in the request
    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
            // Store each file and prepare its path
            $filePath = $file->store('uploads', 'public');
            $filePaths[] = '/storage/' . $filePath;
        }
    }

    // Prepare data for insertion or update
    $data = $request->only(['email', 'title', 'description']);
    $data['file'] = json_encode($filePaths);

    // Check if a record with the given email already exists
    $existingRecord = QA::where('email', $request->input('email'))->first();

    if ($existingRecord) {
        // Decode old file paths
        $oldFilePaths = json_decode($existingRecord->file, true);

        // Delete old files from storage
        foreach ($oldFilePaths as $oldFilePath) {
            // Extract the path relative to the 'public' disk
            $filePath = str_replace('/storage/', '', $oldFilePath);

            if (Storage::disk('public')->exists($filePath)) {
                // Delete the file from the storage
                Storage::disk('public')->delete($filePath);
            }
        }

        // Update the existing record with new data
        $existingRecord->update($data);
    } else {
        // Create a new record with the provided data
        QA::create($data);
    }
}






    // Update an existing record by ID

    public function qaDestroy(Request $request, $id)
    {
        $AdminVendor = QA::find($id);
        if ($AdminVendor) {
            $AdminVendor->delete();
            return response()->json(['status' => 200, 'message' => 'Delete successfully']);
        } else {
            return response()->json(['status' => 404, 'message' => 'Data not found']);
        }
    }

    public function singelQa($id)
    {
        $singelQa = QA::where('id', $id)->first();
        if (!$singelQa) {
            return response()->json(['status' => 400, 'message' => 'Record not found']);
        }

        // Decode the JSON-encoded file field
        $singelQa->file = json_decode($singelQa->file);

        return response()->json(['status' => 200, 'data' => $singelQa]);
    }

    // Client side show QA //

    public function clentQa()
    {
         $user = auth::user()->email;
        $singelQa = QA::where('email', $user)->first();
        if (!$singelQa) {
            return response()->json(['status' => 400, 'message' => 'Record not found']);
        }

        // Decode the JSON-encoded file field
        $singelQa->file = json_decode($singelQa->file);

        return response()->json(['status' => 200, 'data' => $singelQa]);
    }

}
