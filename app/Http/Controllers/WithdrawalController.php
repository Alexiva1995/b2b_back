<?php

namespace App\Http\Controllers;

use App\Models\Liquidaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Tymon\JWTAuth\Facades\JWTAuth;

class WithdrawalController extends Controller
{
    public function getWithdrawals(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->admin == 1) {
            if (isset($request->id)) {
                $withdrawals = Liquidaction::where('user_id', $request->id)->with('user', 'package')->get();
                foreach ($withdrawals as $withdrawal) {
                    $withdrawal->wallet_used = Crypt::decrypt($withdrawal->wallet_used) ?? $withdrawal->wallet_used;
                    $withdrawal->hash = $withdrawal->hash ?? "";
                }
            }
            else {
                $withdrawals = Liquidaction::where('user_id', '>', 1)->with('user', 'package')->get();
                foreach ($withdrawals as $withdrawal) {
                    $withdrawal->wallet_used = Crypt::decrypt($withdrawal->wallet_used) ?? $withdrawal->wallet_used;
                    $withdrawal->hash = $withdrawal->hash ?? "";
                }
            }
        }
        else {
            $withdrawals = Liquidaction::where('user_id', $user->id)->with('user', 'package')->get();
            foreach ($withdrawals as $withdrawal) {
                $withdrawal->wallet_used = Crypt::decrypt($withdrawal->wallet_used) ?? $withdrawal->wallet_used;
                $withdrawal->hash = $withdrawal->hash ?? "";
            }
        }

        return response()->json($withdrawals, 200);
    }
    public function getWithdrawalsDownload() {
        $withdrawals = Liquidaction::with('package', 'user')
            ->get();
        $data = array();
        foreach ($withdrawals as $withdrawal) {
            $item = [
                'id' => $withdrawal->id,
                'created_at' => $withdrawal->created_at->format('d-m-Y'),
                'user' => $withdrawal->user->name,
                'hash' => $withdrawal->hash,
                'wallet' => Crypt::decrypt($withdrawal->wallet_used) ?? $withdrawal->wallet_used,
                'status' => $withdrawal->getStatus(),
                'amount' => $withdrawal->total,
            ];
            array_push($data, $item);
        }
        return response()->json($data, 200);
    }
}
