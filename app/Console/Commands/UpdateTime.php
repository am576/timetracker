<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;

class UpdateTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Task::where('is_active', 1)->increment('time_spent',1);
    }
}
