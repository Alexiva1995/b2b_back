<?php

namespace App\Console\Commands;

use App\Models\Invesment;
use App\Models\Package;
use App\Models\Profitability;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PayProfitMining extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mining:pay';

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
        $today = CarbonImmutable::today();
        $investments = Invesment::where('status', 1)->get();

        foreach ($investments as $investment) {
            $package = Package::find($investment->package_id);
            $days = $package->investment_time == 12 ? ($package->investment_time * 30) + 5 : $package->investment_time * 30;
            $gain = ($investment->invested * (($package->gain / $days) / 100)) + ($investment->invested / $days);
            if (!Profitability::where('invest_id', $investment->id)->whereBetween('created_at', [$today, $today->now()])->exists()) {
                $investment->gain += $gain;
                $investment->save();
                Profitability::create([
                    'user_id' => $investment->user_id,
                    'invest_id' => $investment->id,
                    'amount' => $gain,
                    'amount_available' => $gain,
                    'status' => 4
                ]);
            }
        }
    }
}
