<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KantinController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Middleware\Admin;
use App\Http\Middleware\ApiAuth;
use App\Http\Middleware\Kantin;
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
        Route::get('kantin/makanan/get', [MahasiswaController::class, 'getMakananKantin']);
        Route::get('kantin/makanan/random/get', [MahasiswaController::class, 'getMakananKantinRandomGet']);
        Route::match(['get','post'], 'kantin/makanan/catatan/get', [MahasiswaController::class, 'getMakananKantinCatatan']);
        Route::post('makanan/draft/save', [MahasiswaController::class, 'saveDraftMakanan']);
        Route::post('checkout', [MahasiswaController::class, 'checkout']);
        Route::get('transaksi', [MahasiswaController::class, 'getTransaksi']);
        Route::get('transaksi/detail', [MahasiswaController::class, 'getTransaksiDetail']);
    });
    Route::middleware([Kantin::class])->prefix('kantin')->group(function () {
        Route::get('profil', [KantinController::class, 'getProfil']);
        Route::post('profil', [KantinController::class, 'editProfil']);
        Route::get('makanan', [KantinController::class, 'list']);
        Route::post('makanan/add', [KantinController::class, 'add']);
        Route::post('makanan/edit', [KantinController::class, 'edit']);
        Route::get('riwayat', [KantinController::class, 'listRiwayat']);
        Route::get('pesanan', [KantinController::class, 'listPesanan']);
        Route::post('pesanan/ubah', [KantinController::class, 'updatePesanan']);
        Route::post('pesanan/tolak', [KantinController::class, 'tolakPesanan']);
        Route::post('pengguna/lapor', [KantinController::class, 'laporPengguna']);
        // Route::post('makanan/delete', [KantinController::class, 'delete']);
    });

    Route::middleware([Admin::class])->prefix('admin')->group(function () {
        Route::get('laporan', [AdminController::class, 'listLaporan']);
        Route::get('mahasiswa', [AdminController::class, 'listMahasiswa']);
        Route::get('kantin', [AdminController::class, 'list']);
        Route::post('kantin/add', [AdminController::class, 'add']);
        Route::post('kantin/edit', [AdminController::class, 'edit']);
        Route::post('kantin/delete', [AdminController::class, 'delete']);
    });
});
