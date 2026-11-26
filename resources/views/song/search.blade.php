@extends('layout')

@section('style')
<style>

</style>
@endsection

@section('content')
<div class="card mt-2">
    <div class="card-header d-flex justify-content-between">
        <span class="h2 card-title">{{ __('ui.search_songs') }} - <span class="h4">{{$search}}</span></span>
    </div>
    <div class="card-body">
        <ul>
        @auth
            @php($songTranspositions = auth()->user()->transpositionSongs->keyBy('id'))
        @endauth
        @foreach ($songs as $song)
        <li class="d-flex justify-content-between align-items-center mb-2">
            <a href="{{route('song.show', $song->id)}}">{{$song->name}} - {{$song->artist->name??''}}</a>
            @include('song.playability')
        </li>
        @endforeach
        </ul>
    </div>
</div>
@endsection
