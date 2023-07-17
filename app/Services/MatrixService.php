<?php

namespace App\Services;

use App\Models\MarketPurchased;
use App\Models\User;
class MatrixService
{
    private $bonusService;

    public function __construct(BonusService $bonusService) {
        $this->bonusService = $bonusService;
    }

    public function levelOne()
    {
        // Obtenemos a todos los usuarios que no tengan nivel maximo y que no sean admin.
        $users = User::where('admin', '0')->with('marketPurchased.cyborg')->get();

        foreach ($users as $user) {
            foreach($user->marketPurchased as $matrixPurchased) {
                $userRight = $users->where('father_cyborg_purchased_id', $matrixPurchased->id)->where('binary_side', 'R')->where('buyer_id', $user->id)->where('status', User::ACTIVE )->first();
                $userLeft = $users->where('father_cyborg_purchased_id', $matrixPurchased->id)->where('binary_side', 'L')->where('buyer_id', $user->id)->where('status', User::ACTIVE )->first();
                
                if ($userLeft && $userRight) {
    
                    if ($matrixPurchased->level < 1) {
                        $matrixPurchased->level = 1;
                        $matrixPurchased->save();
                    } 
    
                    $this->levelTwo($matrixPurchased, $users, $userLeft, $userRight);
                }
            }
        }
    }

    public function levelTwo(MarketPurchased $matrixPurchased, $users, User $userLeft, User $userRight)
    {
        $user_left_left = $users->where('buyer_id', $userLeft->id)->where('binary_side', 'L')->where('status', User::ACTIVE )->first();
        $user_left_right = $users->where('buyer_id', $userLeft->id)->where('binary_side', 'R')->where('status', User::ACTIVE )->first();

        $user_right_left = $users->where('buyer_id', $userRight->id)->where('binary_side', 'L')->where('status', User::ACTIVE )->first();
        $user_right_right = $users->where('buyer_id', $userRight->id)->where('binary_side', 'R')->where('status', User::ACTIVE )->first();

        if ($user_left_left && $user_left_right && $user_right_left && $user_right_right) {

            if ($matrixPurchased->level < 2) {
                $matrixPurchased->level = 2;
                $matrixPurchased->save();
                $this->bonusService->subtract($amount = 50,$matrixPurchased->user_id, $matrixPurchased->cyborg->id, $level = 2);
            } 

            $this->levelThree($matrixPurchased, $users, $user_left_left, $user_left_right, $user_right_left, $user_right_right);
        }
    }

    public function levelThree(MarketPurchased $matrixPurchased, $users, User $user_left_left, User $user_left_right, User $user_right_left,  User $user_right_right)
    {
        $user_left_left_left = $users->where('buyer_id', $user_left_left->id)->where('binary_side', 'L')->where('status', User::ACTIVE )->first();
        $user_left_left_right = $users->where('buyer_id', $user_left_left->id)->where('binary_side', 'R')->where('status', User::ACTIVE )->first();

        $user_left_right_left = $users->where('buyer_id', $user_left_right->id)->where('binary_side', 'L')->where('status', User::ACTIVE )->first();
        $user_left_right_right = $users->where('buyer_id', $user_left_right->id)->where('binary_side', 'R')->where('status', User::ACTIVE )->first();

        $user_right_left_left = $users->where('buyer_id', $user_right_left->id)->where('binary_side', 'L')->where('status', User::ACTIVE )->first();
        $user_right_left_right = $users->where('buyer_id', $user_right_left->id)->where('binary_side', 'R')->where('status', User::ACTIVE )->first();

        $user_right_right_left = $users->where('buyer_id', $user_right_right->id)->where('binary_side', 'L')->where('status', User::ACTIVE )->first();
        $user_right_right_right = $users->where('buyer_id', $user_right_right->id)->where('binary_side', 'R')->where('status', User::ACTIVE )->first();

        if ($user_left_left_left && $user_left_left_right && $user_left_right_left && $user_left_right_right && $user_right_left_left && $user_right_left_right && $user_right_right_left && $user_right_right_right) {

            if ($matrixPurchased->level < 3) {
                $matrixPurchased->level = 3;
                $matrixPurchased->save();
                $this->bonusService->subtract($amount = 100,$matrixPurchased->user_id, $matrixPurchased->cyborg->id, $level = 3);
            } 

            $this->levelFour(
                $matrixPurchased,
                $users,
                $user_left_left_left,
                $user_left_left_right,
                $user_left_right_left,
                $user_left_right_right,
                $user_right_left_left,
                $user_right_left_right,
                $user_right_right_left,
                $user_right_right_right
            );
        }
    }

    public function levelFour(MarketPurchased $matrixPurchased, $users, User $user_left_left_left, User $user_left_left_right, User $user_left_right_left, User $user_left_right_right, User $user_right_left_left, User $user_right_left_right, User $user_right_right_left, User $user_right_right_right)
    {
        $user_left_left_left_left = $users->where('buyer_id', $user_left_left_left->id)->where('binary_side', 'L')->where('status', User::ACTIVE )->first();
        $user_left_left_left_right = $users->where('buyer_id', $user_left_left_left->id)->where('binary_side', 'R')->where('status', User::ACTIVE )->first();

        $user_left_left_right_left = $users->where('buyer_id', $user_left_left_right->id)->where('binary_side', 'L')->where('status', User::ACTIVE )->first();
        $user_left_left_right_right = $users->where('buyer_id', $user_left_left_right->id)->where('binary_side', 'R')->where('status', User::ACTIVE )->first();

        $user_left_right_left_left = $users->where('buyer_id', $user_left_right_left->id)->where('binary_side', 'L')->where('status', User::ACTIVE )->first();
        $user_left_right_left_right = $users->where('buyer_id', $user_left_right_left->id)->where('binary_side', 'R')->where('status', User::ACTIVE )->first();

        $user_left_right_right_left = $users->where('buyer_id', $user_left_right_right->id)->where('binary_side', 'L')->where('status', User::ACTIVE )->first();
        $user_left_right_right_right = $users->where('buyer_id', $user_left_right_right->id)->where('binary_side', 'R')->where('status', User::ACTIVE )->first();

        $user_right_left_left_left = $users->where('buyer_id', $user_right_left_left->id)->where('binary_side', 'L')->where('status', User::ACTIVE )->first();
        $user_right_left_left_right = $users->where('buyer_id', $user_right_left_left->id)->where('binary_side', 'R')->where('status', User::ACTIVE )->first();

        $user_right_left_right_left = $users->where('buyer_id', $user_right_left_right->id)->where('binary_side', 'L')->where('status', User::ACTIVE )->first();
        $user_right_left_right_right = $users->where('buyer_id', $user_right_left_right->id)->where('binary_side', 'R')->where('status', User::ACTIVE )->first();

        $user_right_right_left_left = $users->where('buyer_id', $user_right_right_left->id)->where('binary_side', 'L')->where('status', User::ACTIVE )->first();
        $user_right_right_left_right = $users->where('buyer_id', $user_right_right_left->id)->where('binary_side', 'R')->where('status', User::ACTIVE )->first();

        $user_right_right_right_left = $users->where('buyer_id', $user_right_right_right->id)->where('binary_side', 'L')->where('status', User::ACTIVE )->first();
        $user_right_right_right_right = $users->where('buyer_id', $user_right_right_right->id)->where('binary_side', 'R')->where('status', User::ACTIVE )->first();

        if($user_left_left_left_left && $user_left_left_left_right && $user_left_left_right_left && $user_left_left_right_right && $user_left_right_left_left && $user_left_right_left_right
        && $user_left_right_right_left && $user_left_right_right_right && $user_right_left_left_left && $user_right_left_left_right && $user_right_left_right_left
        && $user_right_left_right_right && $user_right_right_left_left && $user_right_right_left_right && $user_right_right_right_left && $user_right_right_right_right) {

            if ($matrixPurchased->level < 4) {
                $matrixPurchased->level = 0;
                $matrixPurchased->type = MarketPurchased::MATRIX_200;
                $matrixPurchased->save();
            } 
        }
    }
}
