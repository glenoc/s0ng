@extends('layout')

@section('style')
<style>
ul {
    padding-left: 0.5rem;
}
</style>
@endsection

@section('content')
<div class="card mt-2">
    <div class="card-header d-flex justify-content-between">
        <span class="h2 card-title">{{ __('ui.songs') }}</span>
        @auth <a class="btn btn-outline-success" href="{{Route('song.create')}}">{{ __('ui.create_song') }}</a> @endauth
    </div>
    <div class="card-body">
        <ul>
        @auth
            @php($songTranspositions = auth()->user()->transpositionSongs->keyBy('id'))
        @endauth
        @foreach ($songs as $song)
        <li class="d-flex justify-content-between align-items-center mb-2">
            <span><a href="{{route('song.show', $song->id)}}">{{$song->name}} - {{$song->artist->name??''}}</a>
                @auth @if ($song->hidden_from_guests) <span class="badge text-bg-secondary">{{ __('ui.song_hidden_from_guests') }}</span> @endif @endauth
            </span>
            @include('song.playability')
        </li>
        @endforeach
        </ul>
    </div>
</div>
@endsection
