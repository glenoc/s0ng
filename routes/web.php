<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SongController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Models\Song;
use App\Models\Artist;
use App\Models\User;
use App\Http\Controllers\SongStartController;

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

Route::name('home')->get('/', function () {
    return redirect(route('song.index'));
});

// Route::prefix('song')->group(function(){
//     Route::name('song.index')->get('/', [SongController::class, 'index']);

//     Route::name('song.insert')->get('insert/{name}', function($name){
//         $newSong = new Song();
//         $newSong->name = $name;
//         $newSong->artist_id = Artist::inRandomOrder()->first()->id;
//         $newSong->text = "TEST";
//         $newSong->save();
//         return $name;
//     });
// });

Route::resource('song', SongController::class);
Route::get('admin/song-notation', [SongController::class, 'notationReview'])
    ->middleware(['auth', 'can:isSuperAdmin'])->name('admin.song-notation');
Route::get('admin/song-notation/data', [SongController::class, 'notationReviewData'])
    ->middleware(['auth', 'can:isSuperAdmin'])->name('admin.song-notation.data');
Route::get('admin/songs/print', [SongController::class, 'printAll'])
    ->middleware(['auth', 'can:isSuperAdmin'])->name('admin.songs.print');
Route::post('song/{song}/transposition', [SongController::class, 'saveTransposition'])->name('song.transposition')->middleware('auth');
Route::post('song/{song}/autoscroll', [SongController::class, 'saveAutoscroll'])->name('song.autoscroll')->middleware('auth');
Route::name('song.search')->get('song.search', [SongController::class, 'search']);
Route::name('song-starts.poll')->get('song-starts', [SongStartController::class, 'poll']);
Route::name('song-starts.store')->post('song/{song}/start', [SongStartController::class, 'store'])->middleware('auth');

Route::resource('artist', ArtistController::class);

Route::resource('users', UserController::class);
Route::put('profile/chords', [UserController::class, 'updateChords'])->name('profile.chords.update')->middleware('auth');

Route::get('superAdmin', [UserController::class, 'superAdminSetup']);

Route::name('login')->get('login', [AuthController::class, 'login']);
Route::name('login_authenticate')->post('login', [AuthController::class, 'authenticate']);
Route::name('logout')->get('logout', [AuthController::class, 'logout']);

// Route::prefix('artist')->group(function(){
//     Route::name('artist.index')->get('/', [ArtistController::class, 'index']);

//     Route::name('artist.create')->get('create', function($name){
//         $newArtist = new Artist();
//         $newArtist->name = $name;
//         $newArtist->save();
//         return $newArtist;
//     });

//     Route::name('artist.store')->post('create', [ArtistController::class, 'store']);

//     Route::name('artist.edit')->get('{artist}', [ArtistController::class, 'edit']);

//     Route::name('artist.update')->put
// });
