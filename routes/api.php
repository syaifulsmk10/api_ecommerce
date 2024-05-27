<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post("/login", [UserController::class, 'postLogin'])->name("login");
Route::post("/register", [UserController::class, 'registerUser'])->name("register");
Route::get('/product/index', [ProductController::class, 'index']);
Route::get('/category', [CategoryController::class, 'index']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');




Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'getUser']);

    Route::get('/cart', [CartController::class, 'getcart']);
    Route::post('/add-to-cart', [CartController::class, 'addToCart']);
    Route::post('/quantity-cart/{id}', [CartController::class, 'updateQuantity']);
    Route::get('/get-coupon', [CartController::class, 'getCoupon']);
    

    Route::prefix('/product')->group(function () {
    Route::post('/create', [ProductController::class, 'create']);
     Route::get('/{product_id}/average', [RatingController::class, 'average']);
    Route::put('/update/{id}', [ProductController::class, 'update']); //belum success
    Route::delete('/delete/{id}', [ProductController::class, 'destroy']);

});
    Route::prefix('/discount')->group(function () {
    Route::get('/', [DiscountController::class, 'index']);
    Route::post('/create', [DiscountController::class, 'create']);
    Route::put('/update/{id}', [DiscountController::class, 'update']);
    Route::delete('/delete/{id}', [DiscountController::class, 'destroy']);
});

 Route::prefix('/rating')->group(function () {
    Route::get('/', [RatingController::class, 'index']);
    Route::post('/create', [RatingController::class, 'create']);
    Route::put('/update/{id}', [RatingController::class, 'update']);
    Route::delete('/delete/{id}', [RatingController::class, 'destroy']);
});


Route::get('/checkOut', [CartController::class, 'checkOut']);
Route::delete('/delete/{id}', [CartController::class, 'destroy']);
Route::delete('/delete-all', [CartController::class, 'destroyAll']);  
   
});