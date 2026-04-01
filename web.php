<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return redirect()->route('register');
})->name('root');

Route::livewire('/settings', 'pages::profile-edit')
    ->middleware(['auth'])
    ->name('settings');

// REGULAR USER ROUTES
Route::livewire('/dashboard', 'pages::student-dashboard')
    ->middleware(['auth'])
    ->name('student.dashboard');

Route::livewire('/student/requests', 'pages::student-requests')
    ->middleware(['auth'])
    ->name('student.requests');

Route::livewire('/student/borrowed', 'pages::currently-borrowed')
    ->middleware(['auth'])
    ->name('student.borrowed');



// ADMIN ONLY ROUTES
Route::livewire('/admin/dashboard', 'pages::admin-dashboard')
    ->middleware(['auth', 'admin'])
    ->name('admin.dashboard');

Route::livewire('/admin/requests', 'pages::admin-requests')
    ->middleware(['auth', 'admin'])
    ->name('admin.requests');

Route::get('/admin/equipment', function () {
    return view('pages.admin-equipment');
})
    ->middleware(['auth', 'admin'])
    ->name('admin.equipment');

Route::livewire('/admin/borrowed', 'pages::currently-borrowed')
    ->middleware(['auth', 'admin'])
    ->name('admin.borrowed');


/*Route::get('/admin/inventory', function () {
    return view('admin.inventory');
})
    ->name('admin.inventory')
    ->middleware(['auth', 'admin']);*/


// PROFILE ROUTES
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
