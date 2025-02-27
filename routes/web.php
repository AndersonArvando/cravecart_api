<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Middleware\Admin;
use App\Http\Middleware\ApiAuth;
use App\Http\Middleware\Mahasiswa;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([ApiAuth::class])->prefix('api')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('login/otp', [AuthController::class, 'login_otp']);

    Route::middleware([Mahasiswa::class])->prefix('mahasiswa')->group(function () {
        Route::get('profil', [MahasiswaController::class, 'getProfil']);
        Route::get('kantin/get', [MahasiswaController::class, 'getKantin']);
    });
    Route::middleware([Mahasiswa::class])->prefix('kantin')->group(function () {
        Route::get('kantin', [AdminController::class, 'list']);
        Route::post('kantin/add', [AdminController::class, 'add']);
        Route::post('kantin/edit', [AdminController::class, 'edit']);
        Route::post('kantin/delete', [AdminController::class, 'delete']);
    });

    Route::middleware([Admin::class])->prefix('admin')->group(function () {
        Route::get('kantin', [AdminController::class, 'list']);
        Route::post('kantin/add', [AdminController::class, 'add']);
        Route::post('kantin/edit', [AdminController::class, 'edit']);
        Route::post('kantin/delete', [AdminController::class, 'delete']);
    });
});
