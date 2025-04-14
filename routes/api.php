<?php

use App\Http\Controllers\AllcodeController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LoginGoogleController;
use App\Http\Controllers\ShowtimeController;
use App\Http\Controllers\SendmailController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SeatingController;
use App\Models\Booking;
use App\Models\Genre;
use App\Models\Location;
use App\Models\Movie;
use App\Models\Showtime;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// Route::get('hienthi', [HomeController::class, 'hienthi']);
Route::get('/hienthi', function () {
    $homeController = new HomeController();
    $output = $homeController->hienthi();
    // Xử lý kết quả hoặc trả về
    return $output;
});
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

//CRUD MOVIE
//get 10 movie
Route::get('listMovie', [MovieController::class, 'listMovie']);
//get all movie
Route::get('getAllMovie', [MovieController::class, 'getAllMovie']);
Route::post('createMovie', [MovieController::class, 'createMovie']);
Route::delete('deleteMovie', [MovieController::class, 'deleteMovie']);
Route::put('updateMovie', [MovieController::class, 'updateMovie']);

Route::get('getMoviesByGenre', [MovieController::class, 'getMoviesByGenre']);
Route::get('detailMovie', [MovieController::class, 'detailMovie']);
Route::get('getMoviesByGenreAndStatus', [MovieController::class, 'getMoviesByGenreAndStatus']);
Route::get('getMoviesByStatus', [MovieController::class, 'getMoviesByStatus']);

//crud showtime
Route::post('createShowtime', [ShowtimeController::class, 'createShowtime']);


//CRUD user

Route::get('show', [UserController::class, 'show']);
Route::delete('destroy/{id}', [UserController::class, 'destroy']);
Route::post('addUser', [UserController::class, 'addUser']);
Route::put('updateData', [UserController::class, 'updateData']);

//role
//Route::get('getAllRole/{type}', [AllcodeController::class, 'getAllRole']);
Route::get('getAllCode', [AllcodeController::class, 'getAllCode']);


//Genre
Route::get('getAllGenre', [GenreController::class, 'getAllGenre']);

//location
Route::get('getAllLocation', [LocationController::class, 'getAllLocation']);

//get infor allshowtime
Route::get('showAllShowtime', [ShowtimeController::class, 'showAllShowtime']);
//get location by movieId
Route::get('showAllShowtimeByMovieId', [ShowtimeController::class, 'showAllShowtimeByMovieId']);
//edit showtime
Route::put('editShowtime', [ShowtimeController::class, 'editShowtime']);
//delete showtime
Route::delete('deleteShowtime', [ShowtimeController::class, 'deleteShowtime']);
//get detail info showtime by id
Route::get('showDetailShowtimeById', [ShowtimeController::class, 'showDetailShowtimeById']);


//forgot password

Route::post('forgotPassword', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('verifyChangePass', [ForgotPasswordController::class, 'verifyChangePass']);

//payment gateway
Route::post('momo_payment', [BookingController::class, 'momo_payment']);

//get al seat
Route::get('GetAllSeat', [SeatingController::class, 'GetAllSeat']);
//Booking
Route::post('paymentBooking', [BookingController::class, 'paymentBooking']);
//get in fo user booking status wait
Route::get('getAllInfoUserBooking', [BookingController::class, 'getAllInfoUserBooking']);
//get confirm booking for user

Route::get('getConfirmBooking', [BookingController::class, 'getConfirmBooking']);

//get detail booking by id
Route::get('getDetailBookingById', [BookingController::class, 'getDetailBookingById']);

//get detail booking by booking id and userid
Route::get('getDetailBookingByIdAndUserId', [BookingController::class, 'getDetailBookingByIdAndUserId']);


//get all user confirm booking
Route::get('getAllInfoUserConfirm', [BookingController::class, 'getAllInfoUserConfirm']);

//get booking gor 1 user
Route::get('getInforBookingUser', [BookingController::class, 'getInforBookingUser']);

Route::middleware('cors')->group(function () {
    // Định nghĩa tuyến đường API của bạn ở đây
    Route::get('/example', 'ExampleController@index');
    Route::post('/example', 'ExampleController@store');

    // Route::get('hienthi', [HomeController::class, 'hienthi']);
});


//login google
Route::get('auth/google', [LoginGoogleController::class, 'redirectToGoogle']);

Route::get('auth/google/callback', [LoginGoogleController::class, 'handleGoogleCallback']);


Route::get('auth', [LoginGoogleController::class, 'redirectToAuth']);
Route::get('auth/callback', [LoginGoogleController::class, 'handleAuthCallback']);