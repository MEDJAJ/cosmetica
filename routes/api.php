<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\Statistique;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/categories',[CategoryController::class, 'index']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);

   
   
    Route::get('/orders', [OrderController::class, 'index']);
    Route::middleware('role:admin')->group(function () {
    Route::post('/categories', [CategoryController::class, 'store']);      
    Route::put('/categories/{id}', [CategoryController::class, 'update']);  
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']); 

    Route::post('/products',[ProductController::class, 'store']);
    Route::put('/products/{id}',[ProductController::class, 'update']);
    Route::delete('/products/{id}',[ProductController::class, 'destroy']);

     Route::get('/getStatistics',[Statistique::class, 'getStatistics']);

       
      });

      Route::middleware('role:client')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
      });

   Route::middleware('role:employee')->group(function () {
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::post('/orders/{id}/prepare', [OrderController::class, 'markAsPrepared']);
    Route::get('/all-orders', [OrderController::class, 'allOrders']);
      });


});