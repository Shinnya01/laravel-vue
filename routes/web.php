<?php

use Inertia\Inertia;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DriveController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// Route::resource('task', TaskController::class);


Route::middleware(['auth', 'verified'])->group(function (){
    Route::prefix('task')->group(function() {
        Route::get('/', [TaskController::class, 'index'])->name('task.index');
        Route::get('/create', [TaskController::class, 'create'])->name('task.create');
        Route::get('/{task}', [TaskController::class, 'show'])->name('task.show');
        Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('task.edit');
        


        Route::post('/', [TaskController::class, 'store'])->name('task.store');
        Route::post('/markAsDone/{task}', [TaskController::class, 'markAsDone'])->name('task.markAsDone');
        Route::put('/{task}', [TaskController::class, 'update'])->name('task.update');
        Route::delete('/delete/{task}', [TaskController::class, 'destroy'])->name('task.delete');
    });

    Route::prefix('drive')->group(function() {
        Route::get('/', [DriveController::class, 'index'])->name('drive.index');
        Route::post('/', [DriveController::class, 'store'])->name('drive.store');
    });
});

require __DIR__.'/settings.php';
