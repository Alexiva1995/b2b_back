<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\inversions;
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
        inversions::create([
            'id' => 1,
            'package' => 'Matrix inicial',
            'description' => 'No genera bono',
            'level' => 1,
            'type' => 0,
            'amount' => 20, 
            'gain' => 0, 
        ]);

        inversions::create([
            'id' => 2,
            'package' => 'Matrix inicial',
            'description' => '4 pagos de 20 USD',
            'level' => 2,
            'type' => 0,
            'amount' => 20, 
            'gain' => 30, 
        ]);

        inversions::create([
            'id' => 3,
            'package' => 'Matrix inicial',
            'description' => '8 pagos de 50 USD',
            'level' => 3,
            'type' => 0,
            'amount' => 20, 
            'gain' => 300, 
        ]);

        inversions::create([
            'id' => 4,
            'package' => 'Matrix inicial',
            'description' => '16 pagos de 100 USD',
            'level' => 4,
            'type' => 0,
            'amount' => 20, 
            'gain' => 1400, 
        ]);

        inversions::create([
            'id' => 5,
            'package' => 'Matrix 200 USD',
            'description' => 'No genera bono',
            'level' => 1,
            'type' => 0,
            'amount' => 200, 
            'gain' => 0, 
        ]);

        inversions::create([
            'id' => 6,
            'package' => 'Matrix 200 USD',
            'description' => '4 pagos de 200 USD',
            'level' => 2,
            'type' => 0,
            'amount' => 200, 
            'gain' => 300, 
        ]);

        inversions::create([
            'id' => 7,
            'package' => 'Matrix 200 USD',
            'description' => '8 pagos de 500 USD',
            'level' => 3,
            'type' => 0,
            'amount' => 200, 
            'gain' => 3000, 
        ]);

        inversions::create([
            'id' => 8,
            'package' => 'Matrix 200 USD',
            'description' => '16 pagos de 1000 USD',
            'level' => 4,
            'type' => 0,
            'amount' => 200, 
            'gain' => 14000, 
        ]);

        inversions::create([
            'id' => 9,
            'package' => 'Matrix 2000 USD',
            'description' => 'No genera bono',
            'level' => 1,
            'type' => 0,
            'amount' => 2000, 
            'gain' => 0, 
        ]);

        inversions::create([
            'id' => 10,
            'package' => 'Matrix 2000 USD',
            'description' => '4 pagos 2000 USD',
            'level' => 2,
            'type' => 0,
            'amount' => 2000, 
            'gain' => 3000, 
        ]);

        inversions::create([
            'id' => 11,
            'package' => 'Matrix 2000 USD',
            'description' => '8 pagos de 5000 USD',
            'level' => 3,
            'type' => 0,
            'amount' => 2000, 
            'gain' => 30000, 
        ]);

        inversions::create([
            'id' => 12,
            'package' => 'Matrix inicial',
            'description' => '16 pagos de 10000 USD',
            'level' => 4,
            'type' => 0,
            'amount' => 2000, 
            'gain' => 140000, 
        ]);
        inversions::create([
            'id' => 12,
            'package' => 'Mineria',
            'description' => '16 pagos de 10000 USD',
            'level' => 4,
            'type' => 0,
            'amount' => 2000, 
            'gain' => 140000, 
        ]);
    }
}
