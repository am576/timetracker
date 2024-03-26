<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TruncateTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:truncate';

    protected $description = 'Truncate the users, projects, and tasks tables';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::table('users')->truncate();
        DB::table('projects')->truncate();
        DB::table('tasks')->truncate();

        $this->info('Tables users, projects, and tasks have been truncated.');
    }
}
