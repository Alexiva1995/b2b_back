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

    public $sponsor, $market;
    /**
     * Aplica el bono por compra el cual ignora al padre direct.
     * @user es el usuario que recibira el bono
     * @order es la orden original de la compra
     * @buyer es el hijo del usuario que recibira la comision, se usa para saber a que matrix pertenece
     * @level es el nivel de la comsision (maximo de 4)
     * @buyer_id es el id del usuario que realizo la compra
     */
    public function generateBonus(int $amount, User $user, $order = null, User $buyer = null, int $level, int $buyer_id = null, $father_cyborg_purchased_id)
    {
        try {
            if ($user->id == 1) return;

            if ($level >= 2) {
                DB::beginTransaction();

                $walletComissionRepository = new WalletComissionRepository;

                $walletComission = new WalletComission([
                    'user_id' => $user->id,
                    'buyer_id' => is_null($buyer->id) ? $buyer->id : $buyer_id,
                    'order_id' => is_null($order) ? null : $order->id,
                    'description' => is_null($order) ? 'Upgrade Commission matrix' : 'First purchase commission',
                    'amount' => $amount,
                    'amount_available' => $amount,
                    'type' => $amount == 20 ? WalletComission::TYPE_MATRIX20 : ($amount == 200 ? WalletComission::TYPE_MATRIX200 : WalletComission::TYPE_MATRIX2000),
                    'status' => WalletComission::STATUS_PENDING,
                    'father_cyborg_purchased_id' => $father_cyborg_purchased_id, // is_null($buyer) ? null : (is_null($buyer->getFatherMarketPurchased()) ? null : $buyer->getFatherMarketPurchased()->id),
                    'level' => $level
                ]);

                $walletComissionRepository->save($walletComission);

                DB::commit();
            }
        } catch (\Throwable $th) {
            DB::rollback();
            Log::info('Fallo al aplicar bono por compra');
            Log::error($th);
        }
    }

    public function generateFirstComission(int $amount, User $user, $order = null, User $buyer = null, int $level, int $buyer_id = null)
    {
        try {
            if ($user->id == 1) return;

            if ($user->padre && $user->padre->id != 1) {
                if($user->padre->padre && $user->padre->padre->id != 1) {
                    DB::beginTransaction();

                    $walletComissionRepository = new WalletComissionRepository;

                    $walletComission = new WalletComission([
                        'user_id' => $user->padre->padre->id,
                        'buyer_id' => is_null($buyer_id) ? null : $buyer_id,
                        'order_id' => is_null($order) ? null : $order->id,
                        'description' => 'First purchase commission',
                        'amount' => $amount,
                        'amount_available' => $amount,
                        'type' => WalletComission::TYPE_MATRIX20,
                        'status' => WalletComission::STATUS_PENDING,
                        'father_cyborg_purchased_id' => $user->padre->getFatherMarketPurchased()->id,
                        'level' => $level
                    ]);

                    $walletComissionRepository->save($walletComission);

                    DB::commit();
                }
            }
        } catch (\Throwable $th) {
            DB::rollback();
            Log::info('Fallo al aplicar bono por compra');
            Log::error($th);
        }
    }



    public function subtract(int $amount, int $user_id, $matrix_id = null, int $level, User $user, int $matrix_type)
    {

            $wallets = WalletComission::where('user_id', $user_id)->where('status', WalletComission::STATUS_PENDING)
                ->where('father_cyborg_purchased_id', $matrix_id)->where('level', $level)->get();

            $amountCal = $amount;
            foreach ($wallets as $wallet) {
                $wallet->status = WalletComission::STATUS_AVAILABLE;
                if ($amountCal >= $wallet->amount_available) {
                    $amountCal = $amountCal - $wallet->amount_available;
                    $wallet->amount_available = 0;
                    $wallet->status = WalletComission::STATUS_PAID;
                } else {
                    $wallet->amount_available = $wallet->amount_available - $amountCal;
                    $amountCal = 0;
                }

                $wallet->save();
            }

        $this->paytoUp($amount, $level, $user, $matrix_type);

    }

    private function paytoUp(int $amount, int $level, User $user, int $matrix_type)
    {
        $walletComissionRepository = new WalletComissionRepository;
        if ($level == 2) {
            //Si el nivel es 2 se paga 3 niveles hacia arriba
            $level_to_scale = 3;
            $sponsor = $this->cycle($user, 0, $level_to_scale);

            if($sponsor !== false && !is_null($this->sponsor)){
                $walletComission = $this->makeComission($this->sponsor, $level_to_scale, $amount, $matrix_type, $user->id, $this->market);

                $walletComissionRepository->save($walletComission);

                $total_amount = $walletComissionRepository->getByReinversionLevel($this->sponsor, $level_to_scale, $amount, WalletComission::STATUS_PENDING);

                $amount_to_subtract = $this->getAmountToSubtractOnLevel2($total_amount, $matrix_type);

               /*  if ($amount_to_subtract) {
                           subtract(int $amount,        int $user_id,        $matrix_id = null, int $level,      User $user, int $matrix_type)
                    $this->subtract($amount_to_subtract, $this->sponsor->id, $matrix_id = null, $level_to_scale, $this->sponsor, $matrix_type);
                } */
            }
        }

        if ($level == 3) {
            //Si el nivel es 3 se paga 4 niveles hacia arriba
            $level_to_scale = 4;
            $sponsor = $this->cycle($user, 0, $level_to_scale);

            if($sponsor !== false && !is_null($this->sponsor)){

                $walletComission = $this->makeComission($this->sponsor, $level_to_scale, $amount, $matrix_type, $user->id, $this->market);

                $walletComissionRepository->save($walletComission);

                $total_amount = $walletComissionRepository->getByReinversionLevel($this->sponsor, $level_to_scale, $amount, WalletComission::STATUS_PENDING);

                $amount_to_subtract = $this->getAmountToSubtractOnLevel3($total_amount, $matrix_type);

            //    /*  if ($amount_to_subtract) {
            //         $this->subtract($amount_to_subtract, $sponsor->id, $matrix_id = null, $level_to_scale, $this->sponsor);
            //     } */
            }

        }

        if ($level == 4 && $matrix_type != MarketPurchased::MATRIX_2000) {
            $level_to_scale = 2;
            $sponsor = $this->cycle($user, 0, $level_to_scale);
            $amount_to_generate = 0;
            if ($matrix_type == MarketPurchased::MATRIX_20) {
                $amount_to_generate = 200;
            } else {
                $amount_to_generate = 2000;
            }
            if($sponsor !== false && !is_null($this->sponsor)){
                $this->generateBonus($amount_to_generate, $this->sponsor, $order = null, $buyer = $user, $level_to_scale, $buyer_id = $user->id, $this->market);
             }
        }
    }
    /*
    * Esta funcion recorre hacia arriba la cantidad de niveles requeridos y retorna al usuario que recibira el pago.
    */
    private function cycle($user, int $iterator, int $iterations)
    {   $get_id_sponsor_matrix = $iterations-1;
        if($iterator == $get_id_sponsor_matrix){
            if(isset($user->father_cyborg_purchased_id)){
                $this->market = $user->father_cyborg_purchased_id;
           }
        }
        if (isset($user->padre) && $iterator < $iterations) {
            $this->cycle($user->padre, $iterator + 1, $iterations);
        } else if ($iterator == $iterations) {
            $this->sponsor = User::find($user->id);
            return $user;
        } else {
            return false;
        }
    }

    private function makeComission(User $sponsor, int $level_to_scale, $amount, int  $matrix_type, int $buyerId, $father_cyborg_purchased_id)
    {
        $walletCount = WalletComission::where([
                ['user_id', $sponsor->id],
                ['type', $matrix_type],
                ['level', $level_to_scale],
                ['father_cyborg_purchased_id', $father_cyborg_purchased_id]
            ])->count();
            return new WalletComission([
                'user_id' => $sponsor->id,
                'buyer_id' => $buyerId,
                'order_id' => null,
                'description' => "Commission for level reinvestment {$level_to_scale}",
                'amount' => $amount,
                'amount_available' => $amount,
                'type' => $matrix_type,
                'status' => $walletCount >= 2 ? WalletComission::STATUS_AVAILABLE : WalletComission::STATUS_PENDING,
                'father_cyborg_purchased_id' => $father_cyborg_purchased_id ?? null,
                'level' => $level_to_scale
            ]);
    }

    private function getAmountToSubtractOnLevel2($total_amount, $matrix_type)
    {
        if ($total_amount == 400 && $matrix_type == MarketPurchased::MATRIX_20) {
            $amount_to_subtract = 100;
        } else if ($total_amount == 4_000 && $matrix_type == MarketPurchased::MATRIX_200) {
            $amount_to_subtract = 1_000;
        } else if ($total_amount == 40_000 && $matrix_type == MarketPurchased::MATRIX_2000) {
            $amount_to_subtract = 10_000;
        } else {
            return false;
        }
    }

    private function getAmountToSubtractOnLevel3($total_amount, $matrix_type)
    {
        if ($total_amount == 1_600 && $matrix_type == MarketPurchased::MATRIX_20) {
            $amount_to_subtract = 200;
        } else if ($total_amount == 16_000 && $matrix_type == MarketPurchased::MATRIX_200) {
            $amount_to_subtract = 2_000;
        } else if ($total_amount == 160_000 && $matrix_type == MarketPurchased::MATRIX_2000) {
            $amount_to_subtract = 20_000;
        } else {
            return false;
        }
    }

    public function generateBonusDirect($amount, $user, $level, $order)
    {
        WalletComission::create([
            'user_id' => $user->buyer_id,
            'buyer_id' => $user->id,
            'order_id' => $order->id,
            'level' => $level,
            'description' => 'Bonus Direct',
            'amount' => $amount,
            'amount_available' => $amount,
            'status' => 0,
            'type' => 1,
            'father_cyborg_purchased_id' => $user->father_cyborg_purchased_id,
        ]);
    }
}
