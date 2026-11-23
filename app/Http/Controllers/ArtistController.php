<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artist;

class ArtistController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function index()
    {
        $artists = Artist::all();
        return view('artists.index', ['artists' => $artists]);
    }

    /**
        * Show the form for creating a new resource.
        *
        * @return Response
        */
    public function create()
    {
        return view('artists.create');
    }

    /**
        * Store a newly created resource in storage.
        *
        * @return Response
        */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:artists,name|max:255',
        ]);

        $newArtist = new Artist();
        $newArtist->name = $request->name;
        $newArtist->save();

        return redirect(route('artist.index'));
    }

    /**
        * Display the specified resource.
        *
        * @param  Artist $artist
        * @return Response
        */
    public function show(Artist $artist)
    {
        //
        $artist->load('songs');
        return view('artists.show', ['artist' => $artist]);
    }

    /**
        * Show the form for editing the specified resource.
        *
        * @param  Artist $artist
        * @return Response
        */
    public function edit(Artist $artist)
    {
        return view('artists.edit', ['artist' => $artist]);
    }

    /**
        * Update the specified resource in storage.
        *
        * @param  Artist $artist
        * @param  Request $request
        * @return Response
        */
    public function update(Artist $artist, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:artists,name,'.$artist->id.'|max:255',
        ]);
        $artist->name =$request->name;
        $artist->save();

        return redirect(route('artist.index'));
    }

    /**
        * Remove the specified resource from storage.
        *
        * @param  Artist $artist
        * @return Response
        */
    public function destroy(Artist $artist)
    {
        $artist->delete();
        
        return redirect(route('artist.index'));
    }
}
