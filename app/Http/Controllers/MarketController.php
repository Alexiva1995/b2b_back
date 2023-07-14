<?php

namespace App\Http\Controllers;

use App\Models\Market;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Order;
// use app\Services\CoinpaymentsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MarketController extends Controller
{
    protected $CoinpaymentsService;

    // public function __construct(CoinpaymentsService $CoinpaymentsService)
    // {
    //     $this->CoinpaymentsService = $CoinpaymentsService;
    // }

    public function getAllCyborgs()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $lastApprovedCyborg = Order::where('user_id', $user->id)
            ->where('status', 1)
            ->latest('cyborg_id')
            ->first();

        $cyborgs = Market::all();
        $data = [];

        $available = false;
        foreach ($cyborgs as $cyborg) {
            if ($available) {
                $available = false;
            } elseif ($lastApprovedCyborg && $lastApprovedCyborg->cyborg_id + 1 == $cyborg->id) {
                $available = true;
            }

            $item = [
                'cyborg_id' => $cyborg->id,
                'product_name' => $cyborg->product_name,
                'amount' => $cyborg->amount,
                'available' => $available,
            ];

            $data[] = $item;
        }

        return response()->json($data, 200);
    }

    public function purchaseCyborg(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $cyborgId = $request->input('cyborg_id', 1);

        $cyborg = Market::find($cyborgId);

         // Crear la orden en la tabla "orders"
        $order = new Order();
        $order->user_id = $user->id;
        $order->cyborg_id = $cyborgId;
        $order->status = 0;
        $order->amount = $cyborg->amount;
        $order->save();

         // Ejecutar la lógica de la pasarela de pago y obtener la respuesta
        //  $this->CoinpaymentsService->create_transaction($cyborg->amount, $cyborg, $request, $order);

    }

    public function checkOrder(Request $request)
    {
        $order = Order::where([['user_id', $request->user()->id], ['status', 0]])->with('coinpaymentTransaction')->first();

        if(is_null($order)){
            return response()->json(['data' => null], 200);
        }

        return response()->json(['data' => $order], 200);
    }

}

