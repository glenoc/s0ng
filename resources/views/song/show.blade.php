@extends('layout')

@section('content-container-class', 'container-fluid px-1')
@section('content-row-class', 'gx-0')

@section('style')
<style>
    .song-text {
        font-family: 'PT Mono', monospace;
        white-space: pre;
        display: block;
    }

    @media only screen and (max-width: 600px) {
        #song-lyrics {
            overflow-x: auto;
        }
    }

    .chord-line {
        font-weight: 1000;
    }

    #songdiv {
        padding-bottom: 50vh;
    }
</style>
@endsection

@section('content')

<div id="songdiv">
    <div class="card mt-1">
        <div class="card-header p-1">
            <h1 class="mb-1">{{$song->name}}</h1>
            <h3 class="mb-1">{{$song->artist->name}}</h3>
            @auth
                <form class="d-inline" method="POST" action="{{ route('song-starts.store', $song) }}">
                    @csrf
                    <button class="btn btn-outline-primary" type="submit">{{ __('ui.start_song') }}</button>
                </form>
                <a class="btn btn-outline-secondary" href="{{route('song.edit', $song->id)}}">{{ __('ui.edit') }}</a>
            @endauth
        </div>
        <div class="card-body p-1">
            <div id="song-lyrics">
                @foreach ($lines as $line)
                    <span class="{{$line->isChord?'chord-line':'text-line'}} song-text">{{$line->text}}</span>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
