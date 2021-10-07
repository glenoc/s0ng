<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SongController;
use App\Http\Controllers\ArtistController;
use App\Models\Song;
use App\Models\Artist;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('song')->group(function(){
    Route::name('song.index')->get('/', [SongController::class, 'index']);

    Route::name('song.insert')->get('insert/{name}', function($name){
        $newSong = new Song();
        $newSong->name = $name;
        $newSong->artist_id = Artist::inRandomOrder()->first()->id;
        $newSong->text = "TEST";
        $newSong->save();
        return $name;
    });
});

Route::prefix('artist')->group(function(){
    Route::name('artist.index')->get('/', [ArtistController::class, 'index']);

    Route::name('artist.insert')->get('insert/{name}', function($name){
        $newArtist = new Artist();
        $newArtist->name = $name;
        $newArtist->save();
        return $newArtist;
    });
});
