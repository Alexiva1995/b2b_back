<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'=> 'user',
            'last_name'=> 'admin',
            'email'=> 'admin@fyt.com',
            'user_name'=> 'admin',
            'admin' => '1',
            // 'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
            'status' => '1',
            'binary_id' => 0,
            'affiliate' => '2',
            'binary_side' => 'L'
        ]);

        User::create([
            'name'=> 'user',
            'last_name'=> 'uno',
            'email'=> 'user@fyt.com',
            'user_name'=> 'useruno',
            'admin'=> '0',
            // 'password' => Hash::make('123456789'),
            'email_verified_at' => now(),
            'status' => '1',
            'buyer_id' => '1',
            'affiliate' => '1',
            'binary_id' => 2,
        ]);

        // User::create([
        //     'name'=> 'user3',
        //     'last_name'=> 'user3',
        //     'email'=> 'user3@fyt.com',
        //     'user_name'=> 'user3',
        //     'admin'=> '0',
        //     'kyc' => '0',
        //     // 'password' => Hash::make('123456789'),
        //     'email_verified_at' => now(),
        //     'status' => '1',
        //     'buyer_id' => '2',
        //     'affiliate' => '1',
        //     'binary_id' => 2,
        // ]);

        // User::create([
        //     'name'=> 'user4',
        //     'last_name'=> 'user4',
        //     'email'=> 'user4@fyt.com',
        //     'user_name'=> 'user4',
        //     'admin'=> '0',
        //     'kyc' => '1',
        //     // 'password' => Hash::make('123456789'),
        //     'email_verified_at' => now(),
        //     'status' => '1',
        //     'buyer_id' => '3',
        //     'affiliate' => '1',
        //     'binary_id' => 3,
        // ]);

        // User::create([
        //     'name'=> 'user5',
        //     'last_name'=> 'user5',
        //     'email'=> 'user5@fyt.com',
        //     'user_name'=> 'user5',
        //     'admin'=> '0',
        //     'kyc' => '0',
        //     // 'password' => Hash::make('123456789'),
        //     'email_verified_at' => now(),
        //     'status' => '1',
        //     'buyer_id' => '4',
        //     'affiliate' => '1',
        //     'binary_id' => 4,
        // ]);
    }
}
