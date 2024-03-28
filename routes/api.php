<?php

use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;
use App\Models\Task;

Route::get('tasks/{id}/time-spent', function ($id) {
    $task = Task::findOrFail($id);
    return response()->json(['time_spent' => $task->time_spent]);
});

Route::post('tasks/{id}/start', [ProjectController::class, 'startTask']);
Route::post('tasks/{id}/stop', [ProjectController::class, 'stopTask']);