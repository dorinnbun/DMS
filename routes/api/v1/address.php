<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AddressController;

Route::prefix('address')->controller(AddressController::class)->group(
  function () {
    Route::get('/', 'dataTable')->name("address.getAllAddress");
    Route::get('/province', 'getProvince')->name("address.getProvince");
    Route::get('/district/{province_id}', 'getDistrictByProvince')->name("address.getProvinceDistrict");
    Route::get('/communes/{district_id}', 'getCommunesByDistrict')->name("address.getDistrictCommunes");
  }
);
