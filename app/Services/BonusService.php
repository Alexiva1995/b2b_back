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
     * @user es el usuario que recibira el bono
     * @order es la orden original de la compra
     * @buyer es el hijo del usuario que recibira la comision, se usa para saber a que matrix pertenece
     * @level es el nivel de la comsision (maximo de 4)
     * @buyer_id es el id del usuario que realizo la compra
     * El monto siempre es de 20 y se genera el status pending
     */
    public function generateBonus(User $user, Order $order, User $buyer, int $level, int $buyer_id)
    {
        try {
            if($user->id == 1) return;

            if($level >= 2) {
            
                DB::beginTransaction();
                
                $walletComissionRepository = new WalletComissionRepository;

                $marketPurchase = MarketPurchased::where('user_id', $user->id);

                $walletComission = new WalletComission([
                    'user_id' => $user->id,
                    'buyer_id' => $buyer_id,
                    'order_id' => $order->id,
                    'description' => 'Comision por compra',
                    'amount' => 20,
                    'amount_available' => 20,
                    'type' => 0, //matrix 20
                    'status' => WalletComission::STATUS_PENDING,
                    'father_cyborg_purchased_id' => is_null($buyer->getFatherMarketPurchased()) ? null : $buyer->getFatherMarketPurchased()->id,
                    'level' => $level
                ]);

                $walletComissionRepository->save($walletComission);

                DB::commit();

            }

            if($user->padre && $level < 4) {
                $this->generateBonus($user->padre, $order, $user, $level + 1, $buyer_id);
            }
            
        } catch (\Throwable $th) {
            DB::rollback();
            Log::info('Fallo al aplicar bono directo');
            Log::error($th);
        }
    }

    public function subtract($amount, int $user_id, int $matrix_id, int $level) 
    {
        Log::info('entre aca a sustraer');
        $wallets = WalletComission::where('user_id', $user_id)->where('status', WalletComission::STATUS_PENDING)
                    ->where('father_cyborg_purchased_id', $matrix_id )->where('level', $level)->get();

        foreach ($wallets as $wallet) {
            if ($amount == 0) {
                $wallet->status = WalletComission::STATUS_AVAILABLE;
            } else {
                if ($amount > $wallet->amount_available) {
                    $amount = $amount - $wallet->amount_available;
                    $wallet->amount_available = 0;
                    $wallet->status = WalletComission::STATUS_PAID;
                } else {
                    $wallet->amount_available = $wallet->amount_available - $amount;
                    $amount = 0;
                    $wallet->status = WalletComission::STATUS_AVAILABLE;
                }
            }
            $wallet->save();
        }
    }
}
