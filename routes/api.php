<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WalletController;
Route::post('deposit', [WalletController::class, 'deposit'])->withoutMiddleware('auth:api');;
Route::post('withdraw', [WalletController::class, 'withdraw'])->withoutMiddleware('auth:api');;
Route::post('transfer', [WalletController::class, 'transfer'])->withoutMiddleware('auth:api');;
Route::get('balance/{user_id}', [WalletController::class, 'balance'])->withoutMiddleware('auth:api');;
Route::get('forms',[WalletController::class,'forms']);
