<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\ProductController;

Route::prefix('product')->controller(ProductController::class)->group(
  function () {
    Route::get('/', 'dataTable')->name("product.getAll");
    Route::post('/store', 'create')->name("product.store");
  }
);
