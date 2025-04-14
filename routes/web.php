<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FirmController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\LoginGoogleController;
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

Route::get('/', function () {
    return view('layout');
});

// Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/hienthi', function () {
    $homeController = new HomeController();
    $output = $homeController->hienthi();
    // Xử lý kết quả hoặc trả về
    return $output;
});

// Route::resource('/firm', FirmController::class);
// Route::resource('/genre', GenreController::class);
Route::get('auth/google', [LoginGoogleController::class, 'redirectToGoogle'])->name('login-google');
Route::get('auth/google/callback', [LoginGoogleController::class, 'handleGoogleCallback']);
Route::post('/momo_payment', [BookingController::class, 'momo_payment']);
