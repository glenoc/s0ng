@extends('layout')

@section('content')

<div>
<h1>{{$artist->name}}@auth - <a class="btn btn-outline-secondary" href="{{route('artist.edit', $artist->id)}}">Edit</a>@endauth</h1>
<ul>
@foreach ($artist->songs as $song)
    <li><a href="{{route('song.show', $song->id)}}">{{$song->name}}</a></li>
@endforeach
</ul>
</div>
@endsection
