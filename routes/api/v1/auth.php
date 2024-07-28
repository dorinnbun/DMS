<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AuthController;

Route::prefix('auth')->controller(AuthController::class)->group(
  function () {
    Route::post('login', 'login')->name('auth.login');
    Route::post('register', 'register')->name('auth.register');
    Route::get('logout', 'logout')->name('auth.logout');
    Route::get('refresh', 'refresh')->name('auth.refresh');
  }
);

/* Route::group(['prefix' => 'auth'], // work the same
  function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
  }
); */