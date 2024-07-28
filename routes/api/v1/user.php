<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\UserController;

Route::prefix('user')->controller(UserController::class)->group(
  function () {
    Route::get('/', 'dataTable')->name("user.getAll");
    Route::patch('/{id}', 'update')->name("user.update");
  }
);
