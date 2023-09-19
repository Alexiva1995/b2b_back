<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Package;
use Illuminate\Database\Seeder;

class packageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Package::create([
            'id' => 1,
            'package' => 'Basic',
            'description' => '',
            'level' => 1,
            'type' => 0,
            'amount' => 100,
            'max_amount' => 20000,
            'investment_time' => 3,
            'gain' => 8,
        ]);
        Package::create([
            'id' => 2,
            'package' => 'Advanced',
            'description' => '',
            'level' => 1,
            'type' => 0,
            'amount' => 100,
            'max_amount' => 20000,
            'investment_time' => 6,
            'gain' => 16,
        ]);
        Package::create([
            'id' => 3,
            'package' => 'Expert',
            'description' => '',
            'level' => 1,
            'type' => 0,
            'amount' => 100,
            'max_amount' => 20000,
            'investment_time' => 12,
            'gain' => 35,
        ]);

    }
}
