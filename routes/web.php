<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Redirects to equipment automatically
Route::get('/', function () {
    return redirect('/equipment');
})->name('root');

Route::get('/test-db', function () {
    return DB::select('SELECT * FROM equipment');
});

// ==========================================
// DASHBOARDS & PAGES (Volt Routes)
// ==========================================

Route::middleware(['auth'])->group(function () {

    // --- SETTINGS ---
    Route::get('/settings', function () {
        return view('pages.profile-edit');
    })->name('settings');

    // --- STUDENT/FACULTY ROUTES ---
    Volt::route('/dashboard', 'pages.student-dashboard')
        ->name('student.dashboard');

    Volt::route('/student/requests', 'pages.student-requests')
        ->name('student.requests');

    Volt::route('/student/borrowed', 'pages.student-borrowed')
        ->name('student.borrowed');
});

// --- ADMIN ROUTES ---
Route::middleware(['auth', 'role:admin'])->group(function () {

    Volt::route('/admin/dashboard', 'pages.admin-dashboard')
        ->name('admin.dashboard');

    Volt::route('/admin/requests', 'pages.admin-requests')
        ->name('admin.requests');

    Route::get('/admin/equipment', function () {
        return view('pages.admin-equipment');
    })->name('admin.equipment');

    Volt::route('/admin/borrowed', 'pages.student-borrowed') // Reusing the borrowed component
    ->name('admin.borrowed');
});

// ==========================================
// EQUIPMENT SYSTEM (Traditional Routes)
// ==========================================
Route::middleware(['auth'])->group(function () {

    Route::get('/equipment', function () {
        $items = DB::select("
            SELECT e.*,
                (SELECT COUNT(*) FROM equipment_items ei
                 WHERE ei.equipment_id = e.id AND ei.status = 'Available') AS available_count
            FROM equipment e ORDER BY e.name
        ");
        return view('equipment.index', ['title' => 'Equipment List', 'items' => $items]);
    });

    Route::get('/equipment/{id}', function ($id) {
        $equipment = DB::selectOne('SELECT * FROM equipment WHERE id = ?', [$id]);
        if (!$equipment) abort(404);

        $items = DB::select("
            SELECT ei.*, c.user_name AS checkout_user_name, c.checkout_date
            FROM equipment_items ei
            LEFT JOIN checkouts c ON ei.id = c.equipment_item_id AND c.status = 'Checked Out'
            WHERE ei.equipment_id = ? ORDER BY ei.id
        ", [$id]);

        return view('equipment.show', ['title' => $equipment->name, 'equipment' => $equipment, 'items' => $items]);
    });

    Route::post('/checkout-item/{id}', function (Request $request, $id) {
        $item = DB::selectOne('SELECT * FROM equipment_items WHERE id = ?', [$id]);
        if (!$item || $item->status !== 'Available') return 'Item is not available.';

        DB::table('checkouts')->insert([
            'equipment_item_id' => $id,
            'user_name' => auth()->user()->name,
            'checkout_date' => now(),
            'status' => 'Checked Out',
        ]);

        DB::table('equipment_items')->where('id', $id)->update(['status' => 'Checked Out']);
        return redirect('/equipment/' . $item->equipment_id);
    });

    Route::post('/return-item/{id}', function ($id) {
        $item = DB::selectOne('SELECT * FROM equipment_items WHERE id = ?', [$id]);
        if (!$item) return 'Item not found.';

        DB::table('equipment_items')->where('id', $id)->update(['status' => 'Available']);
        DB::table('checkouts')->where('equipment_item_id', $id)->where('status', 'Checked Out')
            ->update(['status' => 'Returned', 'return_date' => now()]);

        return redirect('/equipment/' . $item->equipment_id);
    });

    // History access for Admin and Faculty
    Route::get('/checkouts', function () {
        $rows = DB::select('
            SELECT c.id, c.user_name, c.checkout_date, c.return_date, c.status,
                   e.name AS equipment_name, i.serial_number
            FROM checkouts c
            JOIN equipment_items i ON c.equipment_item_id = i.id
            JOIN equipment e ON i.equipment_id = e.id
            ORDER BY c.id DESC
        ');
        return view('checkouts.index', ['title' => 'Checkout History', 'rows' => $rows]);
    })->middleware('role:admin|faculty');
});

// ==========================================
// PROFILE & AUTH
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
