<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;

Route::get('api/accounts/{account}/balance', [AccountController::class, 'balance']);
Route::post('api/accounts/{account}/deposit', [TransactionController::class, 'deposit']);
Route::post('api/accounts/{account}/withdraw', [TransactionController::class, 'withdraw']);
