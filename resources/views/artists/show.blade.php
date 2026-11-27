@extends('layout')

@section('style')
<style>
.artist-songs {
    padding-left: 0.5rem;
}
</style>
@endsection

@section('content')
<div class="card mt-2">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h1 class="h2 card-title mb-0">{{ $artist->name }}</h1>
        @auth
            <a class="btn btn-outline-secondary" href="{{ route('artist.edit', $artist->id) }}">{{ __('ui.edit') }}</a>
        @endauth
    </div>
    <div class="card-body">
        <h2 class="h5">{{ __('ui.songs') }}</h2>
        <ul class="artist-songs mb-0">
            @auth
                @php($songTranspositions = auth()->user()->transpositionSongs->keyBy('id'))
            @endauth
            @foreach ($artist->songs as $song)
                <li class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                    <span><a href="{{ route('song.show', $song->id) }}">{{ $song->name }}</a>
                        @auth @if ($song->hidden_from_guests) <span class="badge text-bg-secondary">{{ __('ui.song_hidden_from_guests') }}</span> @endif @endauth
                    </span>
                    @include('song.playability')
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
