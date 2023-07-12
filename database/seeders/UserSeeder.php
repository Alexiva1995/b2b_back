<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');

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
            'binary_id' => 1,
        ]);

         User::create([
             'name'=> 'user3',
             'last_name'=> 'user3',
             'email'=> 'user3@fyt.com',
             'user_name'=> 'user3',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '2',
             'binary_id' => 2,
             'binary_side' => 'L'
         ]);

         User::create([
             'name'=> 'user4',
             'last_name'=> 'user4',
             'email'=> 'user4@fyt.com',
             'user_name'=> 'user4',
             'admin'=> '0',
             'kyc' => '1',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '2',
             'binary_id' => 2,
             'binary_side' => 'R'
         ]);

         User::create([
             'name'=> 'user5',
             'last_name'=> 'user5',
             'email'=> 'user5@fyt.com',
             'user_name'=> 'user5',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '3',
             'binary_id' => 3,
             'binary_side' => 'L',
         ]);
         User::create([
             'name'=> 'user6',
             'last_name'=> 'user6',
             'email'=> 'user6@fyt.com',
             'user_name'=> 'user6',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '3',
             'binary_id' => 3,
             'binary_side' => 'R',
         ]);
         User::create([
             'name'=> 'user7',
             'last_name'=> 'user7',
             'email'=> 'user7@fyt.com',
             'user_name'=> 'user7',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '4',
             'binary_id' => 4,
             'binary_side' => 'L',
         ]);
         User::create([
             'name'=> 'user8',
             'last_name'=> 'user8',
             'email'=> 'user8@fyt.com',
             'user_name'=> 'user8',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '4',
             'binary_id' => 4,
             'binary_side' => 'R',
         ]);
         User::create([
             'name'=> 'user9',
             'last_name'=> 'user9',
             'email'=> 'user9@fyt.com',
             'user_name'=> 'user9',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '5',
             'binary_id' => 5,
             'binary_side' => 'L',
         ]);
         User::create([
             'name'=> 'user10',
             'last_name'=> 'user10',
             'email'=> 'user10@fyt.com',
             'user_name'=> 'user10',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '5',
             'binary_id' => 5,
             'binary_side' => 'R',
         ]);
         User::create([
             'name'=> 'user11',
             'last_name'=> 'user11',
             'email'=> 'user11@fyt.com',
             'user_name'=> 'user11',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '6',
             'binary_id' => 6,
             'binary_side' => 'L',
         ]);
         User::create([
             'name'=> 'user12',
             'last_name'=> 'user12',
             'email'=> 'user12@fyt.com',
             'user_name'=> 'user12',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '6',
             'binary_id' => 6,
             'binary_side' => 'R',
         ]);
         User::create([
             'name'=> 'user13',
             'last_name'=> 'user13',
             'email'=> 'user13@fyt.com',
             'user_name'=> 'user13',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '7',
             'binary_id' =>7,
             'binary_side' => 'L',
         ]);
         User::create([
             'name'=> 'user14',
             'last_name'=> 'user14',
             'email'=> 'user14@fyt.com',
             'user_name'=> 'user14',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '7',
             'binary_id' => 7,
             'binary_side' => 'R',
         ]);
         User::create([
             'name'=> 'user15',
             'last_name'=> 'user15',
             'email'=> 'user15@fyt.com',
             'user_name'=> 'user15',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '8',
             'binary_id' => 8,
             'binary_side' => 'L',
         ]);
         User::create([
             'name'=> 'user16',
             'last_name'=> 'user16',
             'email'=> 'user16@fyt.com',
             'user_name'=> 'user16',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '8',
             'binary_id' => 8,
             'binary_side' => 'R',
         ]);
         User::create([
             'name'=> 'user17',
             'last_name'=> 'user17',
             'email'=> 'user17@fyt.com',
             'user_name'=> 'user17',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '9',
             'binary_id' => 9,
             'binary_side' => 'L',
         ]);
         User::create([
             'name'=> 'user18',
             'last_name'=> 'user18',
             'email'=> 'user18@fyt.com',
             'user_name'=> 'user18',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '9',
             'binary_id' => 9,
             'binary_side' => 'R',
         ]);
         User::create([
             'name'=> 'user19',
             'last_name'=> 'user19',
             'email'=> 'user19@fyt.com',
             'user_name'=> 'user19',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '10',
             'binary_id' => 10,
             'binary_side' => 'L',
         ]);
         User::create([
             'name'=> 'user20',
             'last_name'=> 'user20',
             'email'=> 'user20@fyt.com',
             'user_name'=> 'user20',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '10',
             'binary_id' => 10,
             'binary_side' => 'R',
         ]);
         User::create([
             'name'=> 'user21',
             'last_name'=> 'user21',
             'email'=> 'user21@fyt.com',
             'user_name'=> 'user21',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '11',
             'binary_id' => 11,
             'binary_side' => 'L',
         ]);
         User::create([
             'name'=> 'user22',
             'last_name'=> 'user22',
             'email'=> 'user22@fyt.com',
             'user_name'=> 'user22',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '11',
             'binary_id' => 11,
             'binary_side' => 'R',
         ]);
         User::create([
             'name'=> 'user23',
             'last_name'=> 'user23',
             'email'=> 'user23@fyt.com',
             'user_name'=> 'user23',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '12',
             'binary_id' => 12,
             'binary_side' => 'L',
         ]);
         User::create([
             'name'=> 'user24',
             'last_name'=> 'user24',
             'email'=> 'user24@fyt.com',
             'user_name'=> 'user24',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '12',
             'binary_id' => 12,
             'binary_side' => 'R',
         ]);
         User::create([
             'name'=> 'user25',
             'last_name'=> 'user25',
             'email'=> 'user25@fyt.com',
             'user_name'=> 'user25',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '13',
             'binary_id' => 13,
             'binary_side' => 'L',
         ]);
         User::create([
             'name'=> 'user26',
             'last_name'=> 'user26',
             'email'=> 'user26@fyt.com',
             'user_name'=> 'user26',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '13',
             'binary_id' => 13,
             'binary_side' => 'R',
         ]);
         User::create([
             'name'=> 'user27',
             'last_name'=> 'user27',
             'email'=> 'user27@fyt.com',
             'user_name'=> 'user27',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '14',
             'binary_id' => 14,
             'binary_side' => 'L',
         ]);
         User::create([
             'name'=> 'user28',
             'last_name'=> 'user28',
             'email'=> 'user28@fyt.com',
             'user_name'=> 'user28',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '14',
             'binary_id' => 14,
             'binary_side' => 'R',
         ]);
         User::create([
             'name'=> 'user29',
             'last_name'=> 'user29',
             'email'=> 'user29@fyt.com',
             'user_name'=> 'user29',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '15',
             'binary_id' => 15,
             'binary_side' => 'L',
         ]);
         User::create([
             'name'=> 'user30',
             'last_name'=> 'user30',
             'email'=> 'user30@fyt.com',
             'user_name'=> 'user30',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '15',
             'binary_id' => 15,
             'binary_side' => 'R',
         ]);
         User::create([
             'name'=> 'user31',
             'last_name'=> 'user31',
             'email'=> 'user31@fyt.com',
             'user_name'=> 'user31',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '16',
             'binary_id' => 16,
             'binary_side' => 'L',
         ]);
         User::create([
             'name'=> 'user32',
             'last_name'=> 'user32',
             'email'=> 'user32@fyt.com',
             'user_name'=> 'user32',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '16',
             'binary_id' => 16,
             'binary_side' => 'R',
         ]);
         User::create([
             'name'=> 'user33',
             'last_name'=> 'user33',
             'email'=> 'user33@fyt.com',
             'user_name'=> 'user33',
             'admin'=> '0',
             'kyc' => '0',
             // 'password' => Hash::make('123456789'),
             'email_verified_at' => now(),
             'status' => '1',
             'buyer_id' => '17',
             'binary_id' => 17,
             'binary_side' => 'R',
         ]);
        }
     }