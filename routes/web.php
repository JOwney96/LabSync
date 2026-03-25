<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('register');
})->name('root');

// REGULAR USER ROUTES
Route::livewire('/dashboard', 'pages::student-dashboard')
    ->middleware(['auth'])
    ->name('student.dashboard');

Route::livewire('/student/requests', 'pages::student-requests')
    ->middleware(['auth'])
    ->name('student.requests');


// ADMIN ONLY ROUTES
Route::livewire('/admin/dashboard', 'pages::admin-dashboard')
    ->name('admin.dashboard')
    ->middleware(['auth', 'admin']);

Route::get('/admin/inventory', function () {
    return view('admin.inventory');
})
    ->name('admin.inventory')
    ->middleware(['auth', 'admin']);


// PROFILE ROUTES
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
