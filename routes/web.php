<?php

use App\Models\Medias;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get("add-media", function() {
    Medias::create([
        "photo1" => "1",
        "photo2" => "2",
    ])->addMedia(storage_path("demo/0_vwcP0i8cGx1TKAte.png"))->toMediaCollection();
});
