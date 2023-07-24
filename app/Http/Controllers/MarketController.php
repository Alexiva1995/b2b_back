<?php

namespace App\Http\Controllers;

use App\Models\Market;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Order;
use App\Models\MarketPurchased;
use App\Models\User;
use App\Services\BonusService;
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

    public function getAllCyborgs($id = null)
{
    if($id == null) {
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
        $paymentResponse = $this->CoinpaymentsService->create_transaction($cyborg->amount, $cyborg, $request, $order);

            // Verificar si la transacción fue exitosa y actualizar el estado de la orden a 1
    /* if ($paymentResponse['success']) {
        $order->status = 1;
        $order->save();

        // Crear una entrada en la tabla market_purchaseds
        $marketPurchased = new MarketPurchased();
        $marketPurchased->user_id = $user->id;
        $marketPurchased->order_id = $order->id;
        $marketPurchased->cyborg_id = $order->cyborg_id;
        $marketPurchased->level = 0;
        $marketPurchased->type = 1;
        $marketPurchased->approved_at = now();
        $marketPurchased->save();
    } */

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

