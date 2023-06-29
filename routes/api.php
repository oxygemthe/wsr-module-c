<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FileAccessController;
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
    Route::get('/files/disk', [FileController::class, 'getUserFiles']);
    Route::get('/shared', [FileController::class, 'getSharedFiles']);
    Route::post('/files', [FileController::class, 'uploadFiles']);
    Route::patch('/files/{file:file_id}', [FileController::class, 'renameFile']);
    Route::delete('/files/{file:file_id}', [FileController::class, 'deleteFile']);
    Route::get('/files/{file:file_id}', [FileController::class, 'getFile'])->name('get-file');

    Route::post('/files/{file:file_id}/accesses', [FileAccessController::class, 'provideAccess']);
    Route::delete('/files/{file:file_id}/accesses', [FileAccessController::class, 'takeAwayAccess']);
});