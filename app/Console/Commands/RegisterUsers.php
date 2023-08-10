<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\ImportUsers;
class RegisterUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:import';

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
    {   $url = base_path('Usuarios.xlsx');
        (new ImportUsers)->import($url, null, \Maatwebsite\Excel\Excel::XLSX, 'UTF-8');

    }
}
