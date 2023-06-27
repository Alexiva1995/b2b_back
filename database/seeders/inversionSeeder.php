<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Inversion;
use Illuminate\Database\Seeder;

class inversionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Inversion::create([
            'id' => 1,
            'user_id' => 1,
            'package_id' => 4,
            'type' => 1,
            'orden_id' => 22,
            'amount' => 200
        ]);
    }
}
