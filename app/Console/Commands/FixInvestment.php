<?php

namespace App\Console\Commands;

use App\Models\Invesment;
use App\Models\Package;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class FixInvestment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'investment:fix';

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
       $investments = Invesment::where('status', 1)->get();

       foreach ($investments as  $investment) {
        if($investment->expiration_date == $investment->created_at->format('Y-m-d')){
            $package = Package::find($investment->package_id);
            $date = CarbonImmutable::parse($investment->created_at);
            $investment->expiration_date = $date->addMonths($package->investment_time)->format('Y-m-d');
            $investment->save();
        }
       }
    }
}