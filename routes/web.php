<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    switch ($user->role) {
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'pengurus':
            return redirect()->route('pengurus.dashboard');
        case 'duta':
            return redirect()->route('duta.dashboard');
        default:
            return redirect()->route('login');
    }
})->name('dashboard');

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:admin'])->prefix('ad')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('admin.dashboard');

    Route::resource('users', UserController::class);
});

Route::middleware(['auth', 'role:pengurus'])->prefix('pe')->group(function () {
    // Define routes for 'pengurus' role
    Route::get('/dashboard', function () {
        return Inertia::render('Pengurus/Dashboard');
    })->name('pengurus.dashboard');
});

Route::middleware(['auth', 'role:duta'])->prefix('du')->group(function () {
    // Define routes for 'duta' role
    Route::get('/dashboard', function () {
        return Inertia::render('Duta/Dashboard');
    })->name('duta.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';