<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\TabunganController;
use App\Http\Controllers\TransaksiPinjamanController;
use App\Http\Controllers\TransaksiTabunganController;
use App\Http\Controllers\UserController;
use App\Models\Pinjaman;
use App\Models\TransaksiTabungan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/dashboard',[DashboardController::class, 'index'] )->middleware('auth');

/* LOGIN CONTROLLER */
Route::get('/',[LoginController::class,'index'])->name('login');
Route::post('/login',[LoginController::class, 'authenticate'] );
Route::post('/logout',[LoginController::class, 'logout'] );

/* KARYAWAN CONTROLLER */
Route::resource('/dashboard/data-karyawan', KaryawanController::class)->middleware('admin');


/* USER CONTROLLER */
Route::get('/dashboard/riwayat-transaksi',[UserController::class,'riwayat']);
Route::get('/dashboard/riwayat-transaksi/filter',[UserController::class,'riwayatFilter']);
Route::get('/setting',[UserController::class,'setting']);
Route::put('/update-bio',[UserController::class,'update']);

/* NASABAH CONTROLLER */
Route::get('/dashboard/data-nasabah/filter', [NasabahController::class,'filter'])->middleware('auth');
Route::get('/dashboard/bagikan-shu', [NasabahController::class,'bagikanShu'])->middleware('auth');
Route::post('/dashboard/transaksi/bagikan-shu',[NasabahController::class,'storeShu'])->middleware('auth');
Route::post('/dashboard/transaksi/batal-shu',[NasabahController::class,'batalShu'])->middleware('auth');
Route::post('/dashboard/data-nasabah/ambil-shu', [NasabahController::class,'ambilShu'])->middleware('auth');
Route::resource('/dashboard/data-nasabah', NasabahController::class)->middleware('auth');

/* PINJAMAN CONTROLLER */
Route::get('/dashboard/data-pinjaman/search', [PinjamanController::class,'search'])->middleware('auth');
Route::get('/dashboard/data-pinjaman/buat', [PinjamanController::class,'buat'])->middleware('auth');
Route::resource('/dashboard/data-pinjaman', PinjamanController::class)->middleware('auth');
Route::get('/dashboard/transaksi-pinjaman/tambah-angsuran', [TransaksiPinjamanController::class,'tambahAngsuran'])->middleware('administrasi');
        /* TRANSAKSI PINJAMAN */
Route::get('/dashboard/transaksi-pinjaman/search', [TransaksiPinjamanController::class,'search'])->middleware('administrasi');
Route::resource('/dashboard/transaksi-pinjaman', TransaksiPinjamanController::class)->middleware('administrasi');

/* TABUNGAN CONTROLLER */
Route::get('/dashboard/data-tabungan/filter', [TabunganController::class,'filter'])->middleware('auth');
Route::get('/dashboard/data-tabungan/search', [TabunganController::class,'search'])->middleware('auth');
Route::get('/dashboard/data-tabungan/buat', [TabunganController::class,'buat'])->middleware('auth');
Route::resource('/dashboard/data-tabungan', TabunganController::class)->middleware('auth');


        /* TRANSAKSI TABUNGAN */
Route::get('/dashboard/transaksi-tabungan/reguler', [TransaksiTabunganController::class,'reguler'])->middleware('auth');
Route::get('/dashboard/transaksi-tabungan/bunga-reguler', [TransaksiTabunganController::class,'bungaReguler'])->middleware('auth');
Route::post('/dashboard/transaksi-tabungan/batal-bunga-reguler', [TransaksiTabunganController::class,'batalBungaReguler'])->middleware('auth');
Route::get('/dashboard/transaksi-tabungan/program', [TransaksiTabunganController::class,'program'])->middleware('auth');
Route::get('/dashboard/transaksi-tabungan/berjangka', [TransaksiTabunganController::class,'berjangka'])->middleware('auth');
Route::get('/dashboard/transaksi-tabungan/search', [TransaksiTabunganController::class,'search'])->middleware('auth');
Route::get('/dashboard/setoran-kolektor', [TransaksiTabunganController::class,'setoranKolektor'])->middleware('auth');
Route::post('/dashboard/transaksi-tabungan/setorkan', [TransaksiTabunganController::class,'setorkan'])->middleware('auth');
Route::resource('/dashboard/transaksi-tabungan', TransaksiTabunganController::class)->middleware('auth');
