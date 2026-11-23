@extends('layout')

@section('style')
<style>
    #textInput {
        font-family: 'PT Mono', monospace;
    }
</style>
@endsection

@section('content')

<div class="card mt-2">
    <div class="card-header d-flex justify-content-between">
        <span class="h2 card-title">Create song</span>
    </div>
    <div class="card-body">
        <form action="{{route('song.store')}}" method="POST">
        {{ csrf_field() }}
        <div class="mb-3">
        <label for="titleInput" class="form-label">Name</label>
        <input type="text" class="form-control" id="titleInput" name="name">
        </div>
        <div class="mb-3">
            <label for="artistInput" class="form-label">Artist</label>
            <input type="text" class="form-control" id="artistInput" name="artist" list="artistInputData">
            <datalist id="artistInputData">
                @foreach ($artists as $artist)
                    <option value="{{$artist->name}}">
                @endforeach
            </datalist>
            {{-- <select class="form-select" id="artistInput" name="artist" aria-label="Default select example">
                @foreach ($artists as $artist)
                    <option value="{{$artist->id}}">{{$artist->name}}</option>
                @endforeach
            </select> --}}
        </div>
        <div class="mb-3">
        <label for="textInput" class="form-label">Text</label>
        <textarea class="form-control" id="textInput" name="text" rows="50"></textarea>
        </div>
        <input type="submit" class="btn btn-primary">
        </form>
    </div>
</div>
@endsection
