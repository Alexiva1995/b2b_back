<?php

namespace App\Console\Commands;

use App\Models\CoinpaymentWithdrawal;
use App\Services\CoinpaymentsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckWithdrawal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'withdrawal:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $service;
    public function __construct(CoinpaymentsService $service)
    {
        $this->service = $service;
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

       $process =  CoinpaymentWithdrawal::where([['status', '>=', 0],['status', '<=', 1]])->get();
       
       foreach ($process as $withdrawal) {
        $this->service->checkWithdrawal($withdrawal->tx_id);
       }
    }
}
