<?php

use Illuminate\Support\Facades\Route;
use App\Presentation\Http\Controllers\FrameController;
use App\Presentation\Http\Controllers\AuthController;

Route::get('/', [FrameController::class, 'landing'])->name('landing');
Route::get('/frames', [FrameController::class, 'gallery'])->name('frames.index');
Route::get('/studio', [FrameController::class, 'studio'])->name('studio')->middleware('auth');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/api/frames', [FrameController::class, 'index']);
Route::post('/api/frames', [FrameController::class, 'store'])->middleware('auth');
Route::post('/api/frames/{id}/download', [FrameController::class, 'download']);