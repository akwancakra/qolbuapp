<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\BoardMember\BeneficiaryController as BoardMemberBeneficiaryController;
use App\Http\Controllers\BoardMember\IncomeController as BoardMemberIncomeController;
use App\Http\Controllers\Ambassador\BeneficiaryController as AmbassadorBeneficiaryController;
use App\Http\Controllers\Ambassador\IncomeController as AmbassadorIncomeController;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    switch ($user->role) {
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'pengurus':
            return redirect()->route('board_member.dashboard');
        case 'duta':
            return redirect()->route('ambassador.dashboard');
        default:
            return redirect()->route('login');
    }
})->name('dashboard');

// CREATE INCOME FOR USERS THAT NOT LOGGED IN
Route::get('/incomes/create', [MainController::class, 'create'])->name('incomes.create');
Route::post('/incomes/store', [MainController::class, 'store'])->name('incomes.store');

Route::middleware(['auth', 'role:admin'])->prefix('ad')->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return Inertia::render('Admin/Dashboard', ['user' => $user]);
    })->name('admin.dashboard');

    Route::resource('users', AdminUserController::class)->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'show' => 'admin.users.show',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);
    Route::post('/users/destroy-multiple', [AdminUserController::class, 'destroyMultiple'])->name('admin.users.destroy-multiple');
});

Route::middleware(['auth', 'role:duta'])->prefix('am')->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return Inertia::render('Ambassador/Dashboard', ['user' => $user]);
    })->name('ambassador.dashboard');

    Route::resource('beneficiaries', AmbassadorBeneficiaryController::class)
        ->only(['index', 'show'])
        ->names([
            'index' => 'ambassador.beneficiaries.index',
            'show' => 'ambassador.beneficiaries.show',
        ]);

    Route::resource('incomes', AmbassadorIncomeController::class)
        ->only(['index', 'create', 'store'])
        ->names([
            'index' => 'ambassador.incomes.index',
            'create' => 'ambassador.incomes.create',
            'store' => 'ambassador.incomes.store',
        ]);
});

Route::middleware(['auth', 'role:pengurus'])->prefix('bm')->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return Inertia::render('BoardMember/Dashboard', ['user' => $user]);
    })->name('board_member.dashboard');

    Route::resource('beneficiaries', BoardMemberBeneficiaryController::class)
        ->except('update')
        ->names([
            'index' => 'board_member.beneficiaries.index',
            'create' => 'board_member.beneficiaries.create',
            'store' => 'board_member.beneficiaries.store',
            'show' => 'board_member.beneficiaries.show',
            'edit' => 'board_member.beneficiaries.edit',
            'destroy' => 'board_member.beneficiaries.destroy',
        ]);
    Route::post('/beneficiaries/destroy-multiple', [BoardMemberBeneficiaryController::class, 'destroyMultiple'])->name('board_member.beneficiaries.destroy-multiple');
    Route::post('/beneficiaries/{beneficiary}', [BoardMemberBeneficiaryController::class, 'update'])->name('board_member.beneficiaries.update');

    Route::resource('incomes', BoardMemberIncomeController::class)
        ->except('show', 'update')
        ->names([
            'index' => 'board_member.incomes.index',
            'create' => 'board_member.incomes.create',
            'store' => 'board_member.incomes.store',
            'edit' => 'board_member.incomes.edit',
            'destroy' => 'board_member.incomes.destroy',
        ]);

    Route::post('/incomes/destroy-multiple', [BoardMemberIncomeController::class, 'destroyMultiple'])->name('board_member.incomes.destroy-multiple');
    Route::post('/incomes/{income}', [BoardMemberIncomeController::class, 'update'])->name('board_member.incomes.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
