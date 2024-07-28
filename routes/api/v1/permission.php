<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\RoleController;

Route::prefix('permission')->controller(RoleController::class)->group(
  function () {
    Route::get('/', 'dataTable')->name("permission.getAll");
    Route::post('/', 'create')->name("permission.create");
    Route::put('/{id}', 'update')->name("permission.update");
    Route::delete('/{id}', 'delete')->name("permission.delete");
  }
);
