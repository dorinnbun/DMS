<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\RoleController;

Route::prefix('role')->controller(RoleController::class)->group(
  function () {
    Route::get('/', 'dataTable')->name("role.getAll");
    Route::get('/{id}', 'getRole')->name("role.getById");
    Route::post('/', 'create')->name("role.create");
    Route::put('/{id}', 'update')->name("role.update");
    Route::delete('/{id}', 'delete')->name("role.delete");
  }
);
