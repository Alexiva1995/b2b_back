<?php

namespace App\Services;

use App\Models\MarketPurchased;
use App\Models\User;
use App\Models\Order;
use App\Models\WalletComission;
use App\Repositories\WalletComissionRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Class BonusService.
 */
class BonusService
{
    /**
     * Aplica el bono por compra el cual ignora al padre direct.
     * Siempre es de 20 y se genera el status pending
     */
    public function generateBonus(User $user, Order $order, int $buyer_id, int $level)
    {
        try {

            if($level >= 2) {
            
                DB::beginTransaction();
                
                $walletComissionRepository = new WalletComissionRepository;

                $walletComission = new WalletComission([
                    'user_id' => $user->id,
                    'buyer_id' => $buyer_id,
                    'order_id' => $order->id,
                    'description' => 'Comision por compra',
                    'amount' => 20,
                    'amount_available' => 20,
                    'type' => 0, //matrix 20
                    'status' => WalletComission::STATUS_PENDING,
                    'father_cyborg_purchased_id' => $order->marketPurchased->id,
                    'level' => $level
                ]);

                $walletComissionRepository->save($walletComission);

                DB::commit();

            }

            if($user->padre && $level < 4) {
                $this->generateBonus($user->padre, $order, $buyer_id, $level + 1);
            }
            
        } catch (\Throwable $th) {
            DB::rollback();
            Log::info('Fallo al aplicar bono directo');
            Log::error($th);
        }
    }
}
