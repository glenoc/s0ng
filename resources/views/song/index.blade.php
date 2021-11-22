@extends('layout')

@section('style')
<style>

</style>
@endsection

@section('content')
<div class="card mt-2">
    <div class="card-header d-flex justify-content-between">
        <span class="h2 card-title">Songs</span>
        @auth <a class="btn btn-outline-success" href="{{Route('song.create')}}">Create new</a> @endauth
    </div>
    <div class="card-body">
        <ul>
        @foreach ($songs as $song)
        <li><a href="{{route('song.show', $song->id)}}">{{$song->name}} - {{$song->artist->name??''}}</a></li>
        @endforeach
        </ul>
    </div>
</div>
@endsection
