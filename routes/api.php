<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;

Route::get('accounts/{account}/balance', [AccountController::class, 'balance']);
Route::post('accounts/{account}/deposit', [TransactionController::class, 'deposit']);
Route::post('accounts/{account}/withdraw', [TransactionController::class, 'withdrawal']);
