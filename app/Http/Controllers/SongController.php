<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Song;

class SongController extends Controller
{
    //
    public function index(){
        $songs = Song::with('artist')->get();
        return view('song.index', ['songs' => $songs]);
    }
}
