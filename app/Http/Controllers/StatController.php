<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Inertia\Inertia;

class StatController extends Controller
{
    public function getProjectStats()
    {
        $projects = Project::with('tasks')->orderBy('created_at', 'desc')->get();

        $stats = $projects->map(function ($project) {
            $timeSpent = $project->tasks->sum('time_spent');
            return [
                'name' => $project->name,
                'time_spent' => $timeSpent
            ];
        });
        return Inertia::render('Statistics', [
            'stats' => $stats
        ]);
    }
}
