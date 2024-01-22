<?php

namespace App\Http\Controllers;

use App\Models\AmazonCategories;
use App\Models\AmazonInvestment;
use App\Models\AmazonLots;
use App\Models\AmazonProducts;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\WalletComission;
use App\Services\CoinpaymentsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;


class AmazonController extends Controller
{
    protected $CoinpaymentsService;

    public function __construct(CoinpaymentsService $CoinpaymentsService)
    {
        $this->CoinpaymentsService = $CoinpaymentsService;
    }

    /**
     * getCategories
     *
     * @return void
     */
    public function getCategories()
    {
        $categories = AmazonCategories::select('name','image','id', 'description')->get();
        return response()->json(['categories' => $categories], 200);
    }

    /**
     * getLotsType
     *
     * @param  mixed $type
     * @return void
     */
    public function getLotsType($type)
    {
        $lots = AmazonCategories::where('name', $type)->first()->lots()->orderBy('id', 'DESC')->get();
        return response()->json(['lots' => $lots], 200);
    }

    /**
     * getProducts
     *
     * @param  mixed $lot
     * @return void
     */
    public function getProducts($lot)
    {
        $products = AmazonLots::where('name', $lot)->first()->products;
        return response()->json(['products' => $products], 200);
    }

    /**
     * storeCategory
     *
     * @param  mixed $request
     * @return void
     */
    public function storeCategory(Request $request)
    {
        DB::beginTransaction();
        try {
            $category = new AmazonCategories();

            if(!is_null($request->preview)){
                $file = $request->file('preview');
                $name = str_replace(" ", "_", $file->getClientOriginalName());
                $file->move(public_path('storage/amazon/categories/'), $name);
                $category->image = 'storage/amazon/categories/'.$name;
            }

            $category->name = $request->name;
            $category->description = $request->description;

            if($category->save()){
                DB::commit();
                return response()->json(['message' => 'Category create successful']);
            }

            throw new Exception("Error create Category");
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);

            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    /**
     * storeLot
     *
     * @param  mixed $request
     * @return void
     */
    public function storeLot(Request $request)
    {
        DB::beginTransaction();

        try {
            $lot = new AmazonLots();
            $lot->name = $request->name;

            if (!is_null($request->preview)) {
                $file2 = $request->file('preview');
                $name = str_replace(" ", "_", $file2->getClientOriginalName());
                $file2->move(public_path('storage/amazon/lots/'), $name);
                $lot->image  = 'storage/amazon/lots/' . $name;
            }

            $lot->price = $request->price;
            $lot->amazon_category_id = AmazonCategories::where('name', $request->category)->first()->id;
            $lot->save();

            if (is_null(json_decode($request->products))) throw new Exception("Error create Lot, must add at least one product");
            if (count(json_decode($request->products)) > 0) $this->addProductsToLot($lot, json_decode($request->products));

            if ($lot->save()) {
                DB::commit();
                return response()->json(['message' => 'Lot create successful'], 200);
            }

            throw new Exception("Error create Lot");
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    private function addProductsToLot($lot, $products)
    {
        foreach ($products as $product) {
            ;
            $product = new AmazonProducts([
                'name' => $product->name,
                'url' => $product->url,
                'pvp' => $product->pvp,
                'price' => $product->price,
            ]);
            $lot->products()->save($product);
        }
    }

    public function purchasedInvestment(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $category = AmazonCategories::where('name', $request->category)->first();
            $category->product_name = "Lots Amazon Category: $category->name";
            $date = Carbon::now();

            $order = new Order();
            $order->user_id = $user->id;
            $order->amount = $request->amount;
            $order->status = '0';
            $order->type = 'inicio';
            $order->amazon_category_id = $category->id;
            $order->save();

            $investment = new AmazonInvestment();
            $investment->amazon_category_id = $category->id;
            $investment->user_id = $user->id;
            $investment->invested = $request->amount;
            $investment->status = 0;
            $investment->gain = 0;
            $investment->order_id = $order->id;
            $investment->save();

            $response = $this->CoinpaymentsService->create_transaction($request->amount, $category, $request, $order, $user);
            if ($response['status'] == 'error') {
                Log::error($response);
                throw new Exception("Error processing purchase", 400);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error al comprar -' . $th->getMessage());
            Log::error($th);
            return response()->json($th->getMessage(), 400);
        }
    }

    public function checkOrder(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $order = $user->orders()->where([['status', '0']])->first();
        if (is_null($order)) {
            return response()->json(['data' => null], 200);
        }
        $datapayment = $order->coinpaymentTransaction;
        return response()->json(['data' => $datapayment], 200);
    }

    public function getInvestUser(Request $request)
    {
        $invests = AmazonInvestment::select('id','amazon_category_id','invested','status','gain','date_start')->where('user_id', $request->auth_user_id)->get();
        foreach ($invests as $invest) {
            $invest->category = AmazonCategories::select('name')->where('id', $invest->amazon_category_id)->first()->name;
        }

        return response()->json(['invests' => $invests], 200);
    }

    public function canceleInvestment(Request $request)
    {
        DB::beginTransaction();
        try {
            $investment = AmazonInvestment::find($request->investmentId);
            $investment->status = 3;
            if($investment->save()){
                WalletComission::create([
                    'user_id' => $request->auth_user_id,
                    'level' => 0,
                    'description' => 'Return of investment in Amazon Lots',
                    'amount' => $investment->invested * 0.7,
                    'amount_available' => $investment->invested * 0.7,
                ]);
            }
            DB::commit();
            return response()->json(['message' => 'Investment cancel success'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return response()->json(['message' => 'Error cancel Investment, call support'], 400);
        }
    }

    public function getAllActiveInvestment(Request $request)
    {
        $investments = AmazonInvestment::with('user')->where('status', 1)->get();
        foreach ($investments as $invest) {
            $invest->category = AmazonCategories::select('name')->where('id', $invest->amazon_category_id)->first()->name;
        }
        return response()->json(['investments' => $investments], 200);
    }

    public function payYield(Request $request)
    {
        DB::beginTransaction();
        try {
            $investment = AmazonInvestment::where([['id', $request->data['investmentId']], ['status', 1]])->first();
            if(is_null($investment)) throw new Exception("Error investment does not exist");
            $amountGain = number_format($investment->invested * ($request->data['percent'] / 100), 2);
            $investment->gain = $amountGain;
            if($investment->save()){
                WalletComission::create([
                    'user_id' => $investment->user_id,
                    'level' => 0,
                    'description' => 'Return on investment in Amazon Lots',
                    'amount' => $amountGain,
                    'amount_available' => $amountGain,
                ]);
            }
            DB::commit();
            return response()->json(['message' => 'Performance paid off successfully']);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return response()->json(['message' => $th->getMessage()], 400);
        }

    }

    public function deleteLot(Request $request)
    {
        DB::beginTransaction();
        try {
            $lot = AmazonLots::find($request->lot);
            $lot->delete();

            DB::commit();
            return response()->json(['message' => 'Delete lot success']);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return response()->json(['message' => 'Error delete lot']);
        }

    }

    public function deleteCategory(Request $request)
    {
        DB::beginTransaction();
        try {
            $category = AmazonCategories::find($request->category);
            $category->delete();

            DB::commit();
            return response()->json(['message' => 'Delete lot success']);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return response()->json(['message' => 'Error delete lot']);
        }
    }
}