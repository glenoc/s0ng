<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;
use App\Models\Song;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SongController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }
    //
    public function index(){
        $songs = Song::with('artist')->when(Auth::guest(), fn ($query) => $query->visibleToGuests())->orderBy('name')->get();
        return view('song.index', ['songs' => $songs]);
    }

    public function notationReview()
    {
        $songs = Song::with('artist')->orderBy('name')->get();

        return view('song.notation-review', ['songs' => $songs]);
    }

    public function notationReviewData()
    {
        return response()->json(Song::select('text')->get()->map(fn ($song) => $song->chordNotation()));
    }

    public function printAll()
    {
        return view('song.print', ['songs' => Song::with('artist')->orderBy('name')->get()]);
    }

    public function search(Request $request){
        $request->validate(['search' => 'required|string|max:80']);
        $songs = Song::with('artist')->when(Auth::guest(), fn ($query) => $query->visibleToGuests())
            ->where('name', 'like', '%'.$request->search.'%')->orderBy('name')->get();
        return view('song.search', ['songs' => $songs, 'search' => $request->search]);
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
                'hidden_from_guests' => 'sometimes|boolean',
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
            $song->hidden_from_guests = $validated['hidden_from_guests'] ?? false;
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
            abort_if(Auth::guest() && $song->hidden_from_guests, 404);
            $song->load('artist');

            $lines = $song->lines();

            $transposition = Auth::check()
                ? DB::table('song_transpositions')->where('user_id', Auth::id())->where('song_id', $song->id)->first()
                : null;
            $autoscrollSpeed = Auth::check()
                ? DB::table('song_autoscroll_preferences')->where('user_id', Auth::id())->where('song_id', $song->id)->value('speed')
                : null;

            return view('song.show', ['song' => $song, 'lines' => $lines, 'transposition' => $transposition, 'autoscrollSpeed' => $autoscrollSpeed]);
        }

        public function saveTransposition(Request $request, Song $song)
        {
            $validated = $request->validate([
                'semitones' => 'required|integer|between:-12,12',
                'capo' => 'required|integer|between:0,24',
            ]);

            DB::table('song_transpositions')->upsert(
                [['user_id' => $request->user()->id, 'song_id' => $song->id, 'semitones' => $validated['semitones'], 'capo' => $validated['capo'], 'updated_at' => now(), 'created_at' => now()]],
                ['user_id', 'song_id'],
                ['semitones', 'capo', 'updated_at']
            );

            return redirect()->route('song.show', $song);
        }

        public function saveAutoscroll(Request $request, Song $song)
        {
            $validated = $request->validate([
                'speed' => 'required|integer|between:1,100',
            ]);

            DB::table('song_autoscroll_preferences')->upsert(
                [['user_id' => $request->user()->id, 'song_id' => $song->id, 'speed' => $validated['speed'], 'updated_at' => now(), 'created_at' => now()]],
                ['user_id', 'song_id'],
                ['speed', 'updated_at']
            );

            return response()->noContent();
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
                'hidden_from_guests' => 'sometimes|boolean',
            ]);

            $artist = Artist::where('name', $request->artist)->first();
            if ($artist == null){
                $artist = new Artist();
                $artist->name = $request->artist;
                $artist->save();
            }

            $song->name = $request->name;
            $song->text = $request->text;
            $song->hidden_from_guests = $validated['hidden_from_guests'] ?? $song->hidden_from_guests;
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
