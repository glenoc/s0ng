@extends('layout')

@section('style')
<style>

</style>
@endsection

@section('content')
<ul>
@foreach ($songs as $song)
<li><a href="{{route('song.show', $song->id)}}">{{$song->name}} - {{$song->artist->name??''}}</a></li>
@endforeach
</ul>
@endsection
