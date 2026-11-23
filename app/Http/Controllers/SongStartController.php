<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\SongStart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SongStartController extends Controller
{
    public function store(Song $song)
    {
        $song->loadMissing('artist');

        SongStart::create([
            'song_id' => $song->id,
            'started_by' => Auth::id(),
            'song_name' => $song->name,
            'artist_name' => $song->artist->name ?? '',
        ]);

        return back();
    }

    public function poll(Request $request)
    {
        $validated = $request->validate([
            'after' => 'nullable|integer|min:0',
        ]);

        $starts = SongStart::query()
            ->where('id', '>', $validated['after'] ?? 0)
            ->orderBy('id')
            ->limit(100)
            ->get()
            ->map(function (SongStart $start) {
                return [
                    'id' => $start->id,
                    'song_name' => $start->song_name,
                    'artist_name' => $start->artist_name,
                    'url' => $start->song_id ? route('song.show', $start->song_id) : null,
                ];
            });

        return response()->json(['starts' => $starts]);
    }
}
