<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Project;
use App\Models\Task;

class ProjectController extends Controller
{
    public function index()
    {
        return Inertia::render('Projects', [
            'projects' => auth()->user()->projects()->with('tasks')->orderBy('created_at', 'desc')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project = Project::create([
            'name' => $validatedData['name'],
            'user_id' => auth()->user()->id,
            'is_active' => 1
        ]);

        return response()->json(['message' => 'Project created successfully', 'project' => $project], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);

        $project->tasks()->delete();

        $project->delete();

        return response()->json(['message' => 'Project and related tasks deleted successfully']);
    }

    public function addTask(Request $request, string $project_id) {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project = Project::findOrFail($project_id);

        $taskName = $validatedData['name'];
        
        $project->tasks()->create([
            'name' => $taskName,
            'project_id' => $project_id,
            'is_active' => 0
        ]);
        
        return response()->json(['message' => 'Task added successfully']);
    }

    public function deleteTask(Request $request, string $project_id, string $task_id) {
        Task::where('project_id', $project_id)->where('id', $task_id)->delete();
        return response()->json(['message' => 'Task removed successfully']);
    }
}
