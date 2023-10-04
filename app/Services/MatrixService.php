<?php

namespace App\Services;

use App\Models\MarketPurchased;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class MatrixService
{
    private $bonusService;

    public function __construct(BonusService $bonusService)
    {
        $this->bonusService = $bonusService;
    }
    // Reto a quien quiera intentar optimizar este codigo haciendo una funcion recursiva y dinamica, si lo logras, pide un aumento

    public function start()
    {
        $users = User::where('admin', '0')->with('marketPurchased')->get();
        foreach ($users as $user) {
            //Log::alert('user: '. $user->id);
            foreach ($user->marketPurchased as $matrixPurchased) {
                if($matrixPurchased->level == 4 && $matrixPurchased->type < MarketPurchased::MATRIX_2000){
                    $matrixPurchased->level = 0;
                    $matrixPurchased->type = $matrixPurchased->type + 1;
                    $matrixPurchased->save();
                   // Log::alert('Lvl 4 up');
                    continue;
                }

                $referrals = $this->getReferrals($matrixPurchased, $level = 1, $maxLevel = 4, $matrixPurchased->type)->groupBy('level');
                if(isset($referrals['4']) && ($matrixPurchased->level < 4 && $matrixPurchased->level > 2)){
                    //Log::alert('Lvl 4');
                    $referralsLvl4 = $this->countMatrixLevel($referrals['4'], $level = 4, $matrixPurchased->type)->sum();
                    if ($referralsLvl4 >= 2 && $matrixPurchased->level < 4) $this->levelFour($matrixPurchased, $referralsLvl4);
                //    Log::info('Lvl 4: '. $referralsLvl4);
                }
                if(isset($referrals['3']) && ($matrixPurchased->level < 3 && $matrixPurchased->level > 1)){
                    //Log::alert('Lvl 3');
                    $referralsLvl3 = $this->countMatrixLevel($referrals['3'], $level = 3, $matrixPurchased->type)->sum();
                    if ($referralsLvl3 >= 2 && $matrixPurchased->level < 3) $this->levelThree($matrixPurchased, $referralsLvl3);
                     //Log::info('Lvl 3: '. $referralsLvl3);
                }
                if(isset($referrals['2']) && ($matrixPurchased->level < 2 && $matrixPurchased->level > 0)){
                    //Log::alert('Lvl 2');
                    $referralsLvl2 = $this->countMatrixLevel($referrals['1'], $level = 2, $matrixPurchased->type)->sum();
                    if ($referralsLvl2 == 2 && $matrixPurchased->level < 2 ) $this->levelTwo($matrixPurchased);
                    //Log::info('Lvl 2: '. $referralsLvl2);
                }
                if(isset($referrals['1']) && $matrixPurchased->level == 0){
                   // Log::alert('Lvl 1');
                    $referralsLvl1 = $this->countMatrixLevel($referrals['1'], $level = 1, $matrixPurchased->type)->sum();
                    if ($referralsLvl1 == 2 && $matrixPurchased->level < 1) $this->levelOne($matrixPurchased);
                   // Log::info('Lvl 1: '. $referralsLvl1);
                }
            }
        }
    }

    public function levelOne($matrixPurchased)
    {
        // Obtenemos a todos los usuarios que no tengan nivel maximo y que no sean admin.
        if ($matrixPurchased->level < 1) {
            $matrixPurchased->level = 1;
            $matrixPurchased->save();
        }
    }

    public function levelTwo($matrixPurchased)
    {
        if ($matrixPurchased->level < 2) {
            $matrixPurchased->level = 2;
            $matrixPurchased->save();
            $amount = $matrixPurchased->type == MarketPurchased::MATRIX_20 ? 50 : ($matrixPurchased->type == MarketPurchased::MATRIX_200 ? 500 : 5_000);
            $this->bonusService->subtract($amount, $matrixPurchased->user_id, $matrixPurchased->id, $level = 2, $matrixPurchased->user, $matrixPurchased->type);
        }
    }

    public function levelThree($matrixPurchased, $referralsLvl3)
    {
        if ($matrixPurchased->level < 3) {
            Log::alert($referralsLvl3. 'ref');
            if($referralsLvl3 == 8) {
                $matrixPurchased->level = 3;
                $matrixPurchased->save();
            }
            $amount = $matrixPurchased->type == MarketPurchased::MATRIX_20 ? 100 : ($matrixPurchased->type == MarketPurchased::MATRIX_200 ? 1_000 : 10_000);
            $this->bonusService->subtract($amount, $matrixPurchased->user_id, $matrixPurchased->id, $level = 3, $matrixPurchased->user, $matrixPurchased->type);
        }
    }

    public function levelFour($matrixPurchased, $referralsLvl4)
    {
        if ($matrixPurchased->level < 4 && $matrixPurchased->type < MarketPurchased::MATRIX_2000) {
            $amount = $matrixPurchased->type == MarketPurchased::MATRIX_20 ? 200 : ($matrixPurchased->type == MarketPurchased::MATRIX_200 ? 2_000 : 20_000);
            $this->bonusService->subtract($amount, $matrixPurchased->user_id, $matrixPurchased->id, $level = 4, $matrixPurchased->user, $matrixPurchased->type);
            if($referralsLvl4 == 16) {
                $matrixPurchased->level = 4;
                $matrixPurchased->save();
            }
        }

        if($matrixPurchased->level <  4 && $matrixPurchased->type == MarketPurchased::MATRIX_2000) {
            if($referralsLvl4 == 16) {
                $matrixPurchased->level = 4;
                $matrixPurchased->save();
            }
        }
    }

    public function getReferrals($matrixPurchased, $level, $maxLevel = null, $matrix_type)
    {
        $userReferrals = User::where([['father_cyborg_purchased_id', $matrixPurchased->id], ['status', User::ACTIVE]])->get();

        $referrals = collect($userReferrals->map(function ($referral) use ($level, $matrix_type) {
            return collect([
                'id' => $referral->id,
                'level' => $level,
                'buyer_id' => $referral->buyer_id,
                'matrix' => MarketPurchased::where([['user_id', $referral->id], ['cyborg_id', 1], ['type', $matrix_type]])->first(),
            ]);
        }));
        if ($level >= $maxLevel) return $referrals;

        // Recorrer los referidos y obtener sus referidos recursivamente
        foreach ($referrals as $referral) {
            if(!isset($referral['matrix'])) continue;
            $subReferrals = $this->getReferrals($referral['matrix'], $level + 1, $maxLevel, $matrix_type);
            $referrals = $referrals->concat($subReferrals);
        }

        return $referrals;
    }

    private function countMatrixLevel($referrals, $level, $matrix_type)
    {
        return $referrals->map(function ($referral) use ($level, $matrix_type) {
            if(is_null($referral['matrix'])) return 0;
            if ($referral['matrix']->level >= $level-1 && $referral['matrix']->type == $matrix_type) return  1;
        });
    }
}
