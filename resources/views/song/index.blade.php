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
        <span class="h2 card-title">Songs</span>
        @auth <a class="btn btn-outline-success" href="{{Route('song.create')}}">Create new</a> @endauth
    </div>
    <div class="card-body">
        <ul>
        @foreach ($songs as $song)
        <li class="d-flex justify-content-between align-items-center mb-2">
            <a href="{{route('song.show', $song->id)}}">{{$song->name}} - {{$song->artist->name??''}}</a>
            @auth
                <form method="POST" action="{{ route('song-starts.store', $song) }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-primary" type="submit">Start sangen</button>
                </form>
            @endauth
        </li>
        @endforeach
        </ul>
    </div>
</div>
@endsection
