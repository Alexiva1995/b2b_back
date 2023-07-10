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
            $userRight = $users->where('buyer_id', $user->id)->where('binary_side', 'R')->first();
            $userLeft = $users->where('buyer_id', $user->id)->where('binary_side', 'L')->first();
            if ($userLeft && $userRight) {

                if ($user->matrix_level < 1)  $user->update(['matrix_level' => 1]);

                $this->levelTwo($user, $users, $userLeft, $userRight);
            }
        }
    }

    public function levelTwo(User $currentUser, $users, User $userLeft, User $userRight)
    {
        $user_left_left = $users->where('buyer_id', $userLeft->id)->where('binary_side', 'L')->first();
        $user_left_right = $users->where('buyer_id', $userLeft->id)->where('binary_side', 'R')->first();

        $user_right_left = $users->where('buyer_id', $userRight->id)->where('binary_side', 'L')->first();
        $user_right_right = $users->where('buyer_id', $userRight->id)->where('binary_side', 'R')->first();

        if ($user_left_left && $user_left_right && $user_right_left && $user_right_right) {

            if ($currentUser->matrix_level < 2) $currentUser->update(['matrix_level' => 2]);

            $this->levelThree($currentUser, $users, $user_left_left, $user_left_right, $user_right_left, $user_right_right);
        }
    }

    public function levelThree(User $currentUser, $users, User $user_left_left, User $user_left_right, User $user_right_left,  User $user_right_right)
    {
        $user_left_left_left = $users->where('buyer_id', $user_left_left->id)->where('binary_side', 'L')->first();
        $user_left_left_right = $users->where('buyer_id', $user_left_left->id)->where('binary_side', 'R')->first();

        $user_left_right_left = $users->where('buyer_id', $user_left_right->id)->where('binary_side', 'L')->first();
        $user_left_right_right = $users->where('buyer_id', $user_left_right->id)->where('binary_side', 'R')->first();

        $user_right_left_left = $users->where('buyer_id', $user_right_left->id)->where('binary_side', 'L')->first();
        $user_right_left_right = $users->where('buyer_id', $user_right_left->id)->where('binary_side', 'R')->first();

        $user_right_right_left = $users->where('buyer_id', $user_right_right->id)->where('binary_side', 'L')->first();
        $user_right_right_right = $users->where('buyer_id', $user_right_right->id)->where('binary_side', 'R')->first();

        if ($user_left_left_left && $user_left_left_right && $user_left_right_left && $user_left_right_right && $user_right_left_left && $user_right_left_right && $user_right_right_left && $user_right_right_right) {
            if ($currentUser->matrix_level < 3) $currentUser->update(['matrix_level' => 3]);

            $this->levelFour(
                $currentUser,
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

    public function levelFour(User $currentUser, $users, User $user_left_left_left, User $user_left_left_right, User $user_left_right_left, User $user_left_right_right, User $user_right_left_left, User $user_right_left_right, User $user_right_right_left, User $user_right_right_right)
    {
        
    }
}
