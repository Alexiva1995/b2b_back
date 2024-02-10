<?php

namespace App\Console\Commands;

use App\Models\Learning;
use Illuminate\Console\Command;

class UpdatePathLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'link:update';

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
        $learnings = Learning::where('type', 2)->get();
        foreach ($learnings as $learning) {
            $learning->path = $learning->file_name;
            $learning->save();
        }
    }
}
