@extends('layout')

@section('style')
<style>

</style>
@endsection

@section('content')
<div class="card mt-2">
    <div class="card-header d-flex justify-content-between">
        <span class="h2 card-title">Songs search - <span class="h4">{{$search}}</span></span>
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
