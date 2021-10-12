@extends('layout')

@section('content')

<div>
<h1>Artists</h1>
<ul>
@foreach ($artists as $artist)
    <li><a href="{{route('artist.show', $artist->id)}}">{{$artist->name}}</a></li>
@endforeach 
</ul>
</div>
@endsection