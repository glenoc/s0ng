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
    <div class="card-header">
        <span class="h2 card-title">{{ __('ui.artists') }}</span>
    </div>
    <div class="card-body">
        <ul class="mb-0">
        @foreach ($artists as $artist)
        <li class="d-flex justify-content-between align-items-center mb-2">
            <a href="{{ route('artist.show', $artist->id) }}">{{ $artist->name }}</a>
            <span class="badge rounded-pill text-bg-secondary">
                {{ trans_choice('ui.song_count', $artist->songs_count) }}
            </span>
        </li>
        @endforeach
        </ul>
    </div>
</div>
@endsection