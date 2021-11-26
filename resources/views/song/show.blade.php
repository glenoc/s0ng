@extends('layout')

@section('style')
<style>
    .song-text {
        font-family: 'PT Mono', monospace;
        white-space: pre;
        display: block;
    }

    @media only screen and (max-width: 600px) {
        .song-text {
            font-size: 2.7vw;
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
<h1>{{$song->name}}</h1>
<h3>{{$song->artist->name}}</h3>@auth<a class="btn btn-outline-secondary" href="{{route('song.edit', $song->id)}}">Edit</a>@endauth
{{-- <pre> --}}
    <div>
@foreach ($lines as $line)
<span class="{{$line->isChord?'chord-line':'text-line'}} song-text">{{$line->text}}</span>
@endforeach
</div>
{{-- </pre> --}}
</div>
@endsection
