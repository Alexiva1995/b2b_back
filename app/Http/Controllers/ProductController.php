<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function storeShippingData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'country' => 'required',
            'document_id' => 'required',
            'postal_code' => 'required',
            'phone_number' => 'required',
            'state' => 'required',
            'street' => 'required',
            'department' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->toArray()[0]], 400);
        }
    
        try {
            DB::beginTransaction();
    
            $product = Product::create([
                'name' => $request->name,
                'country' => $request->country,
                'document_id' => $request->document_id,
                'postal_code' => $request->postal_code,
                'phone_number' => $request->phone_number,
                'status' => 0,
                'state' => $request->state,
                'street' => $request->street,
                'department' => $request->department,
            ]);
    
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Product Created!', 'data' => $product], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message" => "Please try again later"], 500);
        }
    }
}
