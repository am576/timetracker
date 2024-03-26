<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->has(Project::factory()->has(Task::factory()->count(5))->count(5))->create([
            'name' => 'Иван Петров',
            'email' => 'ivanpetrov@timetracker.com',
            'password' => '123',
        ]);

        User::factory()
        ->has(Project::factory()->has(Task::factory()->count(5))->count(5))
        ->count(3)
        ->create();
    }
}
