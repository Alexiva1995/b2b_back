<?php

namespace App\Services;

use App\Models\User;
use App\Models\Investment;
use App\Models\Membership;
use App\Models\Order;
use App\Models\WalletComission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Class BonusService.
 */
class BonusService
{
    /**
     * Aplica el bono directo.
     * El % a recibir dependera del rol o rango del usuario. 
     */
    public function directBonus(User $user, $amount, $buyer_id, $order)
    {
        try {
            DB::beginTransaction();
            
            $referred = $user->padre;
            $can_recieve_bonus = $this->validations($referred, $buyer_id);
            
            // Log::debug('entra aca');
            if($can_recieve_bonus) {
                // Log::debug('si recibe');
                $percent = 0;
                $skip = false;
                // Se aplica un % segun el rango ? del usuario
                switch ($referred->affiliate) {
                    case '1':
                        $percent = 0.10;
                        if ($order->coupon_id != null) {
                            if ($order->coupon->percentage == 5) {
                                $percent = 0.05;
                            } else  {
                                $skip = true;
                            }
                        }
                        break;
                    case '2':
                        $percent = 0.20;
                        if ($order->coupon_id != null) {
                            if ($order->coupon->percentage == 5) {
                                $percent = 0.15;
                            } elseif  ($order->coupon->percentage == 10) {
                                $percent = 0.10;
                            } else {
                                $skip = true;
                            }
                        }
                        break;
                }
                if ($skip == false){
                    $wallet = WalletComission::create([
                        'user_id' => $referred->id,
                        'buyer_id' => $buyer_id,
                        'membership_id' => $order->project->id,
                        'order_id' => $order->id,
                        'description' => 'Bono directo',
                        'type' => '0',
                        'level' => '1',
                        'status' => 0,
                        'avaliable_withdraw' => 0,
                        'amount_available' => $amount * $percent,
                        'amount' => $amount * $percent,
                    ]);
                    if ($referred->affiliate != '2') $this->unilevelBonus($referred, $amount, $buyer_id, $order, $level = 2);
                    DB::commit();
                }
            }
        } catch (\Throwable $th) {
            DB::rollback();
            Log::info('Fallo al aplicar bono directo');
            Log::error($th);
        }
    }
    /**
     * Aplica el bono unilevel
     * El nivel maximo depende de la suscripción del usuario. 
     */
    public function unilevelBonus(User $user, $amount, $buyer_id, $order, $level)
    {
        try {

            $referred = $user->padre;
            
            $can_recieve_bonus = $this->validations($referred, $buyer_id);

            if ($can_recieve_bonus && $level == 2) {
                $gain = $amount * 0.10;
                if($referred->affiliate == 2) 
                {
                    $wallet = WalletComission::make([
                        'user_id' => $referred->id,
                        'buyer_id' => $buyer_id,
                        'membership_id' => $order->project->id,
                        'order_id' => $order->id,
                        'description' => 'Bono Unilevel',
                        'type' => '2',
                        'level' => $level,
                        'status' => 0,
                        'avaliable_withdraw' => 0,
                        'amount_available' => $gain,
                        'amount' => $gain,
                    ]);
                    $wallet->save();
                }
                // El padre puede recibir bono de nivel 2 si tiene el rol de super afiliado
            }
            //El nivel maximo es 2
            // if ($level < 2) {
            //     $this->unilevelBonus($referred, $amount, $buyer_id, $order, $level + 1);
            // }
        } catch (\Throwable $th) {
            // DB::rollback();
            Log::info('Fallo al aplicar bono unilevel');
            Log::error($th);
        }
    }

    private function validations($referred, int $buyer_id)
    {
        $res = true;
        // Si por alguna razon el padre no existe se cancela
        if (!$referred) {
            $res = false;
        }
        if (isset($referred) && $referred->admin == '1') {
            $res = false;
        }
        // Si el id del padre es mayor al del hijo se cancela
        if (isset($referred) && $referred->id > $buyer_id) {
             $res = false;
        }

        /*Se el padre no tiene suscripción activa se cancela.
        if (!$referred->hasActiveSuscription()) {
            $res = false;
        }*/
        // Si el padre no es afiliado o super afiliado se cancela
        if(isset($referred) && $referred->affiliate == 0)
        {
             $res = false;
        }
        return $res;
    }
}
