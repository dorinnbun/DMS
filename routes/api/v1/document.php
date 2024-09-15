<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\DocumentController;

Route::prefix('document')->controller(DocumentController::class)->group(
  function () {
    Route::get('/', 'dataTable')->name("document.getAll");
    Route::get('/getTrashList', 'getTrashList')->name("document.getTrashList");
    Route::get('/test', 'test')->name("document.test");
    Route::get('/{uuid}', 'getByUuid')->name("document.getByUuid");
    Route::get('restore/{id}', 'restore')->name("document.restoreById");
    Route::post('/', 'create')->name("document.create");
    Route::post('/{id}', 'update')->name("document.update");
    Route::delete('/{id}', 'delete')->name("document.delete");
    Route::delete('/hardDelete/{id}', 'hardDelete')->name("document.delete");
  }
);
