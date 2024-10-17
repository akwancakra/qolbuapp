<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Duta\BeneficiaryController as DutaBeneficiaryController;
use App\Http\Controllers\Duta\TransactionController as DutaTransactionController;
use App\Http\Controllers\Pengurus\BeneficiaryController as PengurusBeneficiaryController;
use App\Http\Controllers\Pengurus\TransactionController as PengurusTransactionController;
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

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

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

Route::middleware(['auth', 'role:admin'])->prefix('ad')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('admin.dashboard');

    Route::resource('users', AdminUserController::class);
});

Route::middleware(['auth', 'role:pengurus'])->prefix('pe')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Pengurus/Dashboard');
    })->name('pengurus.dashboard');

    Route::resource('transactions', PengurusTransactionController::class)->names([
        'index' => 'pengurus.transactions.index',
        'create' => 'pengurus.transactions.create',
        'store' => 'pengurus.transactions.store',
        'show' => 'pengurus.transactions.show',
        'edit' => 'pengurus.transactions.edit',
        'update' => 'pengurus.transactions.update',
        'destroy' => 'pengurus.transactions.destroy',
    ]);

    Route::resource('beneficiaries', PengurusBeneficiaryController::class)->names([
        'index' => 'pengurus.beneficiaries.index',
        'create' => 'pengurus.beneficiaries.create',
        'store' => 'pengurus.beneficiaries.store',
        'show' => 'pengurus.beneficiaries.show',
        'edit' => 'pengurus.beneficiaries.edit',
        'update' => 'pengurus.beneficiaries.update',
        'destroy' => 'pengurus.beneficiaries.destroy',
    ]);
});

Route::middleware(['auth', 'role:duta'])->prefix('du')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Duta/Dashboard');
    })->name('duta.dashboard');

    Route::resource('transactions', DutaTransactionController::class)->names([
        'index' => 'duta.transactions.index',
        // 'create' => 'duta.transactions.create',
        // 'store' => 'duta.transactions.store',
        // 'show' => 'duta.transactions.show',
        // 'edit' => 'duta.transactions.edit',
        // 'update' => 'duta.transactions.update',
        // 'destroy' => 'duta.transactions.destroy',
    ]);

    Route::resource('beneficiaries', DutaBeneficiaryController::class)->names([
        'index' => 'duta.beneficiaries.index',
        // 'create' => 'duta.beneficiaries.create',
        // 'store' => 'duta.beneficiaries.store',
        // 'show' => 'duta.beneficiaries.show',
        // 'edit' => 'duta.beneficiaries.edit',
        // 'update' => 'duta.beneficiaries.update',
        // 'destroy' => 'duta.beneficiaries.destroy',
    ]);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';