<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\StationController;
use App\Models\Station;
use GuzzleHttp\Middleware;


Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
Route::post('/auth/logout', [AuthController::class, 'logout']);

// stations
Route::get('/stations',[StationController::class,'index']);

    Route::middleware('admin')->group(function(){
        Route::post('/stations',[StationController::class,'store']);
        Route::put('/stations/{id}',[StationController::class,'update']);
        Route::delete('/stations/{id}',[StationController::class,'destroy']);
    });

// reservation
Route::get('/reservations',[ReservationController::class,'index']);
});