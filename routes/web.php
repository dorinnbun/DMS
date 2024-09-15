<?php

use App\Models\Medias;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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

Route::get('/test', function () {
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
});
