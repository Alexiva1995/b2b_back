<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{
    public function storeShippingData(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'country' => 'required',
            'auth_user_id' => 'required',
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
                'user_id' => $request->auth_user_id,
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
            return response()->json(["message" => $th->getMessage()], 500);
        }
    }

    public function listUsersProductData(Request $request)
    {
        // $data = [];

        // $products = Product::all();

        // foreach ($products as $product) {
        //     $data[] = [
        //         'id' => $product->id,
        //         'user_id' => $product->user_id,
        //         'name' => $product->name,
        //         'country' => $product->country,
        //         'document_id' => $product->document_id,
        //         'postal_code' => $product->postal_code,
        //         'phone_number' => $product->phone_number,
        //         'status' => $product->status,
        //         'state' => $product->state,
        //         'street' => $product->street,
        //         'department' => $product->department,
        //         'created_at' => $product->created_at,
        //         'updated_at' => $product->updated_at,
        //     ];
        // }

        // return response()->json($data, 200);

        $filter = $request->dataToProduct;

        $query = Product::query();

        foreach ([
            'name',
            'user_id',
            'country',
            'document_id',
            'postal_code',
            'phone_number',
            'status',
            'state',
            'street',
            'department',
            'created_at',
            'updated_at',

        ] as $field) {
            $value = $filter[$field] ?? null;

            if ($value !== null) {
                if (in_array($field, ['name', 'street', 'department'])) {
                    $query->where($field, 'like', '%' . $value . '%');
                } elseif (in_array($field, ['created_at', 'updated_at'])) {
                    $query->whereDate($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
        }

        $data = $query->get();

        return response()->json($data, 200);
    }

    public function updateProductStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->toArray()[0]], 400);
        }

        try {
            $product = Product::findOrFail($id);
            $product->status = $request->status;
            $product->save();

            return response()->json(['status' => 'success', 'message' => 'Product status updated successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(["message" => "Product not found"], 404);
        }
    }

}
