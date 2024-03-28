<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\StatController;
use App\Models\Project;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'projects' => Project::with('user')->get()
    ]);
})->name('home');

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'verified']], function () {
    Route::get('/', function () {
        return redirect()->route('projects.index');
    })->name('dashboard');
    Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/add-task', [ProjectController::class, 'addTask'])->name('projects.add-task');
    Route::delete('/projects/{project_id}/{task_id}', [ProjectController::class, 'deleteTask']);
    Route::post('projects/start-task/{task_id}', [ProjectController::class, 'startTask'])->name('projects.start-task');
    Route::get('statistics', [StatController::class, 'getProjectStats'])->name('statistics');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
