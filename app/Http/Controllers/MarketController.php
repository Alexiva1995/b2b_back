<?php

namespace App\Http\Controllers;

use App\Models\Market;
use App\Models\Order;
use app\Services\CoinpaymentsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MarketController extends Controller
{
    
    public function __construct(CoinpaymentsService $CoinpaymentsService)
    {
        $this->CoinpaymentsService = $CoinpaymentsService;
    }

    public function getAllCyborgs()
    {
        $user = Auth::user();
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
            $user = Auth::user();
            $cyborgId = $request->input('cyborg_id', 1);

            $cyborg = Market::find($cyborgId);

              // Crear la orden en la tabla "orders"
              $order = new Order();
              $order->user_id = $user->id;
              $order->cyborg_id = $cyborgId;
              $order->status = 0;
              $order->save();

            // Ejecutar la lógica de la pasarela de pago y obtener la respuesta
            $this->CoinpaymentsService->create__transaccion($cyborg->amount, $cyborg, $request, $order);


            }
        }
