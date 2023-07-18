<?php

namespace Database\Seeders;

use App\Models\MarketPurchased;
use App\Models\Order;
use App\Models\ReferalLink;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Services\BonusService;
use Illuminate\Support\Str;

class TestMatrixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // admin
        $bonusService = new BonusService;

        User::create([
            'name'=> 'user',
            'last_name'=> 'admin',
            'email'=> 'admin@b2b.com',
            'user_name'=> 'admin',
            'admin' => '1',
            // 'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
            'status' => '1',
            'binary_id' => 0,
            'binary_side' => 'L'
        ]);

        $user = User::create([
            'name'=> 'user',
            'last_name'=> 1,
            'email'=> "user1@b2b.com",
            'user_name'=> 'user1',
            'admin'=> '0',
            // 'password' => Hash::make('123456789'),
            'email_verified_at' => now(),
            'status' => '1',
            'buyer_id' => 1,
            'binary_id' => 1,
            'binary_side' => 'L'
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'amount' => '100',
            'hash' => null,
            'status' => '1',
            'cyborg_id' => '1'
        ]);

        $marketPurchased = MarketPurchased::create([
            'user_id' => $user->id,
            'cyborg_id' => 1,
            'order_id' => $order->id
        ]);

        ReferalLink::create([
            'user_id' => $user->id,
            'link_code' => Str::random(6),
            'cyborg_id' => 1,
            'right' => 1,
            'left' => 1,
        ]);

        $bonusService->generateBonus($user, $order, $buyer = $user, $level = 0, $user->id);

        for($i = 3; $i < 33; $i++) {
            
            $father = User::where('id', round($i / 2))->with('marketPurchased')->first();

            $user = User::create([
                'name'=> 'user',
                'last_name'=> $i,
                'email'=> "user{$i}@b2b.com",
                'user_name'=> 'user'.$i,
                'admin'=> '0',
                // 'password' => Hash::make('123456789'),
                'email_verified_at' => now(),
                'status' => '1',
                'buyer_id' => round($i / 2),
                'binary_id' => round($i / 2),
                'binary_side' => ($i % 2) == 0 ? 'R' : 'L',
                'father_cyborg_purchased_id' => $father->marketPurchased->first()->id
            ]);
    
            $order = Order::create([
                'user_id' => $user->id,
                'amount' => '100',
                'hash' => null,
                'status' => '1',
                'cyborg_id' => '1'
            ]);
    
            $marketPurchased = MarketPurchased::create([
                'user_id' => $user->id,
                'cyborg_id' => 1,
                'order_id' => $order->id
            ]);

            ReferalLink::create([
                'user_id' => $user->id,
                'link_code' => Str::random(6),
                'cyborg_id' => 1,
                'right' => 1,
                'left' => 1,
            ]);

            $bonusService->generateBonus($user, $order, $buyer = $user, $level = 0, $user->id);
        }
    }
}
