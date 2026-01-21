<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\CategoryController;

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

// Rute untuk halaman utama
Route::get('/', function () {
    return redirect()->route('login');
});

// Rute untuk autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Rute yang memerlukan autentikasi
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Password
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
    
    // Data Produk
    Route::resource('products', ProductController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    
    // Data Kategori
    Route::resource('categories', CategoryController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    
    // Transaksi Penjualan
    Route::get('sales/export/stock-opname', [SaleController::class, 'exportStockOpname'])->name('sales.exportStockOpname');
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
});