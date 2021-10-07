<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artist;

class ArtistController extends Controller
{
    //
    public function index(){
        $artists = Artist::inRandomOrder()->with('songs')->first();
        return $artists;
        return view('song.index', ['songs' => $songs]);
    }
}
