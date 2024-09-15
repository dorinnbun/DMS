<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
  Route::group(['middleware' => ['logRequest']], function () {
    require __DIR__ . '/address.php';
    
    require __DIR__ . '/role.php';
    require __DIR__ . '/permission.php';
    require __DIR__ . '/auth.php';
    require __DIR__ . '/user.php';
    Route::group(['middleware' => ['auth:api']], function() {
      require __DIR__ . '/document.php';
      require __DIR__ . '/product.php';
    });

  });
  
});
