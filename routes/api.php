<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\UploadController;

// পাবলিক রুট (লগইন/রেজিস্ট্রেশনের জন্য)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Password reset endpoints (send reset link, perform reset)
Route::post('/password/forgot', [AuthController::class, 'forgot']);
Route::post('/password/reset', [AuthController::class, 'reset']);

// প্রোটেক্টেড রুট (শুধুমাত্র লগইন করা ব্যবহারকারীদের জন্য)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::post('/transactions/bulk', [TransactionController::class, 'bulk']);
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show']);
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update']);
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    // Payment methods
    Route::get('/payment-methods', [PaymentMethodController::class, 'index']);
    Route::post('/payment-methods', [PaymentMethodController::class, 'store']);
    Route::put('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update']);
    Route::delete('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroy']);

    // Uploads
    Route::post('/uploads', [UploadController::class, 'store']);
});
