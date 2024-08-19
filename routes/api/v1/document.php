<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\DocumentController;

Route::prefix('document')->controller(DocumentController::class)->group(
  function () {
    Route::get('/', 'dataTable')->name("document.getAll");
    Route::get('/{id}', 'getDocument')->name("document.getById");
    Route::get('restore/{id}', 'restore')->name("document.restoreById");
    Route::post('/', 'create')->name("document.create");
    Route::put('/{id}', 'update')->name("document.update");
    Route::delete('/{id}', 'delete')->name("document.delete");
  }
);
