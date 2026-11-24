<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\AdminVehicleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/armada/{slug}', [HomeController::class, 'vehicle'])->where('slug', '[a-z0-9-]+')->name('armada');
Route::get('/tentang', [HomeController::class, 'about'])->name('tentang');
Route::get('/kontak', [HomeController::class, 'contact'])->name('kontak');

Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->middleware('throttle:5,1');

    Route::get('/lupa-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/lupa-password', [PasswordResetController::class, 'send'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/atur-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/atur-password', [PasswordResetController::class, 'update'])->middleware('throttle:5,1')->name('password.update');
});

Route::post('/keluar', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Foto kendaraan (publik, disajikan dari disk privat)
Route::get('/foto/{vehicle}', [CatalogController::class, 'photo'])->name('foto.kendaraan');

// Bukti bayar (pemilik pesanan atau admin; dicek di controller)
Route::get('/bukti/{payment}', [OrderController::class, 'proof'])->middleware('auth')->name('bukti');

// Invoice (pemilik pesanan atau admin; dicek di controller)
Route::get('/invoice/{order}', [OrderController::class, 'invoice'])->middleware('auth')->name('invoice');

// Pelanggan
Route::middleware(['auth', 'role:pelanggan'])->group(function () {
    Route::get('/katalog', [CatalogController::class, 'index'])->name('pelanggan.katalog');
    Route::get('/kendaraan/{vehicle}', [CatalogController::class, 'show'])->name('pelanggan.kendaraan');

    Route::get('/pesan/{vehicle}', [BookingController::class, 'create'])->name('pelanggan.pesan');
    Route::get('/pesan/{vehicle}/ketersediaan', [BookingController::class, 'availability'])->name('pelanggan.ketersediaan');
    Route::post('/pesan/{vehicle}', [BookingController::class, 'store'])->name('pelanggan.pesan.simpan');

    Route::get('/pesanan', [OrderController::class, 'index'])->name('pelanggan.pesanan');
    Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('pelanggan.pesanan.show');
    Route::post('/pesanan/{order}/bayar', [OrderController::class, 'pay'])->name('pelanggan.pesanan.bayar');
    Route::delete('/pesanan/{order}', [OrderController::class, 'cancel'])->name('pelanggan.pesanan.batal');
});

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/pesanan', [AdminController::class, 'orders'])->name('admin.pesanan');
    Route::get('/laporan', [AdminController::class, 'report'])->name('admin.laporan');
    Route::get('/laporan/unduh', [AdminController::class, 'exportReport'])->name('admin.laporan.unduh');

    Route::patch('/pesanan/{order}/konfirmasi', [AdminController::class, 'confirm'])->name('admin.pesanan.konfirmasi');
    Route::patch('/pesanan/{order}/mulai', [AdminController::class, 'startRental'])->name('admin.pesanan.mulai');
    Route::patch('/pesanan/{order}/batal', [AdminController::class, 'cancel'])->name('admin.pesanan.batal');
    Route::post('/pesanan/{order}/pengembalian', [AdminController::class, 'recordReturn'])->name('admin.pesanan.pengembalian');
    Route::post('/pesanan/{order}/tunai', [AdminController::class, 'cashPayment'])->name('admin.pesanan.tunai');

    Route::patch('/pembayaran/{payment}/verifikasi', [AdminController::class, 'verifyPayment'])->name('admin.bayar.verifikasi');

    // Armada
    Route::get('/kendaraan', [AdminVehicleController::class, 'index'])->name('admin.kendaraan');
    Route::get('/kendaraan/baru', [AdminVehicleController::class, 'create'])->name('admin.kendaraan.baru');
    Route::post('/kendaraan', [AdminVehicleController::class, 'store'])->name('admin.kendaraan.simpan');
    Route::get('/kendaraan/{vehicle}/ubah', [AdminVehicleController::class, 'edit'])->name('admin.kendaraan.ubah');
    Route::put('/kendaraan/{vehicle}', [AdminVehicleController::class, 'update'])->name('admin.kendaraan.perbarui');
    Route::delete('/kendaraan/{vehicle}', [AdminVehicleController::class, 'destroy'])->name('admin.kendaraan.hapus');
    Route::post('/kendaraan/{vehicle}/paket', [AdminVehicleController::class, 'storePackage'])->name('admin.paket.simpan');
    Route::post('/kendaraan/{vehicle}/foto', [AdminVehicleController::class, 'storePhoto'])->name('admin.foto.simpan');
    Route::delete('/kendaraan/{vehicle}/foto', [AdminVehicleController::class, 'destroyPhoto'])->name('admin.foto.hapus');
    Route::put('/paket/{package}', [AdminVehicleController::class, 'updatePackage'])->name('admin.paket.perbarui');
    Route::delete('/paket/{package}', [AdminVehicleController::class, 'destroyPackage'])->name('admin.paket.hapus');
    Route::post('/kendaraan/{vehicle}/unit', [AdminVehicleController::class, 'storeUnit'])->name('admin.unit.simpan');
    Route::patch('/unit/{unit}/status', [AdminVehicleController::class, 'updateUnitStatus'])->name('admin.unit.status');
    Route::delete('/unit/{unit}', [AdminVehicleController::class, 'destroyUnit'])->name('admin.unit.hapus');

    // Pengaturan
    Route::get('/pengaturan', [AdminSettingController::class, 'edit'])->name('admin.pengaturan');
    Route::put('/pengaturan', [AdminSettingController::class, 'update'])->name('admin.pengaturan.simpan');
});