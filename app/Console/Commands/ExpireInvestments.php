<?php

namespace App\Console\Commands;

use App\Models\Invesment;
use App\Models\Profitability;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpireInvestments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mining:expire';

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
       $today = Carbon::today()->format('Y-m-d');
       $investments = Invesment::where([['status', 1], ['expiration_date', $today]])->get();

       foreach ($investments as $investment) {
            $investment->status = 2;
            if($investment->gain != $investment->max_gain) $investment->gain = $investment->max_gain;
            $investment->save();
            Profitability::where([['status', 4], ['invest_id', $investment->id]])->update(['status'=> 0]);
       }
    }
}
