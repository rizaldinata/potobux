<?php

use Illuminate\Support\Facades\Route;

use App\Presentation\Http\Controllers\FrameController;

Route::get('/', function () {
    return view('welcome');
});

use App\Presentation\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/api/frames', [FrameController::class, 'index']);
Route::post('/api/frames', [FrameController::class, 'store'])->middleware('auth');

Route::get('/debug-upload', function() {
    return [
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
    ];
});
