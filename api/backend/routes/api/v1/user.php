<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\UserController;

Route::prefix('user')->controller(UserController::class)->group(
  function () {
    Route::get('/', 'dataTable')->name("user.getAll");
    Route::get('/{id}', 'getUser')->name("user.getUser");
    Route::post('/forget_password', 'forgetPassword')->name("user.forgetPassword");
    Route::post('/verify_otp/{uuid}', 'verifyOtp')->name("user.verifyOtp");
    Route::post('/resetPassword/{uuid}', 'resetPassword')->name("user.forgetPassword");
    Route::patch('/{id}', 'update')->name("user.update");
    Route::delete('/{id}', 'delete')->name("user.delete");
    Route::get('/restore/{id}', 'restore')->name("user.restore");
  }
);
