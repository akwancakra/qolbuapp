<?php

use App\Http\Controllers\Api\BeneficiaryController;
use App\Http\Controllers\Api\IncomeController;
use Illuminate\Support\Facades\Route;

Route::post('/income', [IncomeController::class, 'store']);
Route::post('/incomes/export', [IncomeController::class, 'export']);
Route::get('/beneficiaries/export/{id}', [BeneficiaryController::class, 'exportByNik']);
Route::post('/beneficiaries/export', [BeneficiaryController::class, 'export']);
