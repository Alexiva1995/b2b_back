<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class MatrixService
{
    public function levelOne()
    {
        // Obtenemos a todos los usuarios que no tengan nivel maximo y que no sean admin.
        $users = User::where('admin', '0')->get();
        $users = $users->where('matrix_level', '!=', 4);

        foreach ($users as $user) {
            if ($user->matrix_level == 0) {
                $userRight = $users->where('buyer_id', $user->id)->where('binary_side', 'R')->first();
                $userLeft = $users->where('buyer_id', $user->id)->where('binary_side', 'L')->first();
                if ($userLeft && $userRight) {
                    $user->update(['matrix_level' => 1]);
                    $this->levelTwo($user, $users, $userLeft, $userRight);
                }
            } else if ($user->matrix_level == 1) {

                $userRight = $users->where('buyer_id', $user->id)->where('binary_side', 'R')->first();
                $userLeft = $users->where('buyer_id', $user->id)->where('binary_side', 'L')->first();
                $this->levelTwo($user, $users, $userLeft, $userRight);
            } else if($user->matrix_level == 2) {
                // Logica para nivel 3
            } else if($user->matrix_level == 3){
                // Logica para nivel 4
            }
        }
    }

    public function levelTwo(User $currentUser, $users , User $userLeft, User $userRight)
    {
        $user_left_left = $users->where('buyer_id', $userLeft->id)->where('binary_side', 'L')->first();
        $user_left_right = $users->where('buyer_id', $userLeft->id)->where('binary_side', 'R')->first();

        $user_right_left = $users->where('buyer_id', $userRight->id)->where('binary_side', 'L')->first();
        $user_right_right = $users->where('buyer_id', $userRight->id)->where('binary_side', 'R')->first();

        if($user_left_left && $user_left_right && $user_right_left && $user_right_right) {
            $current_user->update(['matrix_level' => 2]);
            $this->levelThree();
        }
    }

    public function levelThree()
    {

    }
}
