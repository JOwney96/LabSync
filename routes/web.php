<?php

use App\Http\Controllers\ProfileController;
use App\Models\AdminRoutesEnum;
use App\Models\StudentRoutesEnum;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('register');
})->name('root');

// All route names should come from either the `StudentRoutesEnum` or `AdminRoutesEnum` enum.
// This will make it easier to maintain and reuse routes.

Route::livewire('/settings', 'pages::profile-edit')
    ->middleware(['auth'])
    ->name(StudentRoutesEnum::SETTINGS); // The name and route for settings should be the same for both admin and borrowers for reusability.

// REGULAR USER ROUTES
Route::livewire('/dashboard', 'pages::student-dashboard')
    ->middleware(['auth'])
    ->name(StudentRoutesEnum::DASHBOARD->value);

Route::livewire('/student/requests', 'pages::student-requests')
    ->middleware(['auth'])
    ->name(StudentRoutesEnum::REQUESTS->value);

Route::livewire('/student/borrowed', 'pages::currently-borrowed')
    ->middleware(['auth'])
    ->name(StudentRoutesEnum::BORROWED->value);


// ADMIN ONLY ROUTES
Route::livewire('/admin/dashboard', 'pages::admin-dashboard')
    ->middleware(['auth', 'admin'])
    ->name(AdminRoutesEnum::DASHBOARD->value);

Route::livewire('/admin/requests', 'pages::admin-requests')
    ->middleware(['auth', 'admin'])
    ->name(AdminRoutesEnum::REQUESTS->value);

Route::get('/admin/equipment', function () {
    return view('pages.admin-equipment');
})
    ->middleware(['auth', 'admin'])
    ->name(AdminRoutesEnum::EQUIPMENT->value);

Route::livewire('/admin/borrowed', 'pages::currently-borrowed')
    ->middleware(['auth', 'admin'])
    ->name(AdminRoutesEnum::BORROWED->value);


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
