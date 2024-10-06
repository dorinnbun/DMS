<?php

use App\Models\Medias;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\RegisterController;

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

/* Route::get('/test', function () {
    $files = Storage::disk('space')->allFiles();
    dd($files);
    if ($files === false) {
        log_debug('Could not connect to DigitalOcean Space',[]);
    } else {
        log_debug('Successfully connected to DigitalOcean Space', ['files' => $files]);
        print_r($files); // This will print the list of files in your DigitalOcean Space.
    }
});

Route::get("add-media", function() {
    Medias::create([
        "photo1" => "1_XcE0wR1ZmWLFbdF2dE5WuA",
        "photo2" => "modified",
    ])->addMedia(storage_path("demo/1_XcE0wR1ZmWLFbdF2dE5WuA-modified.png"))->toMediaCollection();
}); */

Route::get('/home', [HomeController::class, 'index'])->name('home');
Auth::routes();

Route::post('/2fa', function () {
    return view('/home');
})->name('2fa')->middleware('2fa');

Route::get("/complete-registration", [RegisterController::class, "completeRegistration"])->name("complete.registration");