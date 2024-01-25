<?php

namespace App\Console\Commands;

use App\Models\Market;
use App\Models\Package;
use Illuminate\Console\Command;

class UpdatePrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $market = Market::find(1);
        $market->amount = 100;
        $market->save();

        $packages = Package::all();

        foreach ($packages as $package) {
            if($package->id == 1) $package->gain = 12;
            if($package->id == 2) $package->gain = 20;
            if($package->id == 3) $package->gain = 50;
            $package->save();
        }
    }
}
