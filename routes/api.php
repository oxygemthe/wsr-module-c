<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FileController;
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

Route::middleware('guest')->group(function () {
    Route::post('/authorization', [LoginController::class, 'authentication']);
    Route::post('/registration', [LoginController::class, 'registration']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logout', [LoginController::class, 'logout']);
    Route::post('/files', [FileController::class, 'uploadFiles']);
    Route::get('/files/{file_id}', [FileController::class, 'getFile'])->name('get-file');
});