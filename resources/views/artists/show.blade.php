@extends('layout')

@section('content')

<div>
<h1>{{$artist->name}}@auth - <a class="btn btn-outline-secondary" href="{{route('artist.edit', $artist->id)}}">{{ __('ui.edit') }}</a>@endauth</h1>
<ul>
@auth
    @php($songTranspositions = auth()->user()->transpositionSongs->keyBy('id'))
@endauth
@foreach ($artist->songs as $song)
    <li><a href="{{route('song.show', $song->id)}}">{{$song->name}}</a> @include('song.playability')</li>
@endforeach
</ul>
</div>
@endsection
