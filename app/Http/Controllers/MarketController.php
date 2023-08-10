<?php

namespace App\Http\Controllers;

use App\Models\Market;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Order;
use App\Models\MarketPurchased;
use App\Models\User;
use App\Services\BonusService;
use App\Services\CoinpaymentsService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MarketController extends Controller
{
    protected $CoinpaymentsService;

    public function __construct(CoinpaymentsService $CoinpaymentsService)
    {
        $this->CoinpaymentsService = $CoinpaymentsService;
    }

    public function getAllCyborgs($id=null)
    {
         // Obtener el usuario autenticado si no se proporciona el parámetro "id"
         if ($id == null) {
            $user = JWTAuth::parseToken()->authenticate();
        } else {
            $user = User::find($id);
        }

        $lastApprovedCyborg = Order::where('user_id', $user->id)
            ->where('status', '1')
            ->latest('cyborg_id')
            ->first();

        $nextCyborgId = $lastApprovedCyborg ? $lastApprovedCyborg->cyborg_id + 1 : 1;

        $cyborgs = Market::all();
        $data = [];

        foreach ($cyborgs as $cyborg) {
            $available = ($cyborg->id == $nextCyborgId);
            $isPurchased = ($cyborg->id < $nextCyborgId); // Agregar la condición para isPurchased

            $item = [
                'cyborg_id' => $cyborg->id,
                'product_name' => $cyborg->product_name,
                'amount' => $cyborg->amount,
                'available' => $available,
                'isPurchased' => $isPurchased, // Agregar isPurchased a la colección
            ];

            $data[] = $item;
        }

        return response()->json($data, 200);
    }



    public function purchaseCyborg(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(is_null($request->cyborg_id)){
                return response()->json('cyborg_id is null', 422);
            }
            $cyborgId = $request->cyborg_id;

            $cyborg = Market::find($cyborgId);


            // Crear la orden en la tabla "orders"
            $order = new Order();
            $order->user_id = $user->id;
            $order->cyborg_id = $cyborgId;
            $order->status = '0';
            $order->amount = $cyborg->amount;
            $order->save();

            // Ejecutar la lógica de la pasarela de pago y obtener la respuesta
             $paymentResponse = $this->CoinpaymentsService->create_transaction($cyborg->amount, $cyborg, $request, $order);
            if($paymentResponse['status'] == 'error'){
                throw new Exception("Error processing a payment creation");
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
        $order = Order::where([['user_id', $request->user()->id], ['status', 0]])->with('coinpaymentTransaction')->first();

        if (is_null($order)) {
            return response()->json(['data' => null], 200);
        }

        return response()->json(['data' => $order], 200);
    }

    public function checkOrderMarket(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $order = $user->orders()->where('status', '0')->first();
        if (is_null($order)) {
            return response()->json(['data' => null], 200);
        }
        $datapayment = $order->coinpaymentTransaction;
        return response()->json(['data' => $datapayment], 200);
    }
}
