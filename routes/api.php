<?php

use App\Http\Controllers\Api\IncomeController;
use Illuminate\Support\Facades\Route;

Route::post('/income', [IncomeController::class, 'store']);
