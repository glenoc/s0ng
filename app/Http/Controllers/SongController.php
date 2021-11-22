<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;
use App\Models\Song;
use Illuminate\Support\Facades\Auth;

class SongController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store', 'edit', 'destroy']);
    }
    //
    public function index(){
        $songs = Song::with('artist')->get();
        return view('song.index', ['songs' => $songs]);
    }

        /**
        * Show the form for creating a new resource.
        *
        * @return Response
        */
        public function create()
        {
            $artists = Artist::get(['id', 'name']);
            return view('song.create', ['artists' => $artists]);
        }

        /**
            * Store a newly created resource in storage.
            *
            * @return Response
            */
        public function store(Request $request)
        {
            $validated = $request->validate([
                'name' => 'required|unique:songs,name|max:255',
                'artist' => 'required|max:255',
                'text' => 'required',
            ]);

            $artist = Artist::where('name', $request->artist)->first();
            if ($artist == null){
                $artist = new Artist();
                $artist->name = $request->artist;
                $artist->save();
            }

            $song = new Song();
            $song->name = $request->name;
            $song->text = $request->text;
            $song->artist()->associate($artist);
            $song->save();

            return redirect(route('song.index'));
        }

        /**
            * Display the specified resource.
            *
            * @param  Song $song
            * @return Response
            */
        public function show(Song $song)
        {
            $song->load('artist');

            $lines = [];

            $exploded = preg_split("/\r\n|\n|\r/", $song->text);

            foreach($exploded as $line){
                $isChord = str_ends_with($line, '_');

                if (Auth::check() || (!Auth::check() && !$isChord)) {
                    $lines[] = (object)['isChord' => $isChord, 'text' => $isChord?chop($line, '_'):$line];
                }
            }

            return view('song.show', ['song' => $song, 'lines' => $lines]);
        }

        /**
            * Show the form for editing the specified resource.
            *
            * @param  Song $song
            * @return Response
            */
        public function edit(Song $song)
        {
            $artists = Artist::get(['id', 'name']);

            return view('song.edit', ['song' => $song->load('artist'), 'artists' => $artists]);
        }

        /**
            * Update the specified resource in storage.
            *
            * @param  Song $song
            * @param  Request $request
            * @return Response
            */
        public function update(Song $song, Request $request)
        {
            $validated = $request->validate([
                'name' => 'required|unique:songs,name,'.$song->id.'|max:255',
                'artist' => 'required|max:255',
                'text' => 'required',
            ]);

            $artist = Artist::where('name', $request->artist)->first();
            if ($artist == null){
                $artist = new Artist();
                $artist->name = $request->artist;
                $artist->save();
            }

            $song->name = $request->name;
            $song->text = $request->text;
            $song->artist()->associate($artist);
            $song->save();

            return redirect(route('song.index'));
        }

        /**
            * Remove the specified resource from storage.
            *
            * @param  Song $song
            * @return Response
            */
        public function destroy(Song $song)
        {
            $song->delete();

            return redirect(route('song.index'));
        }
}
