@extends('layout')

@section('style')
<style>
    #textInput {
        font-family: 'PT Mono', monospace;
    }

    .edit-content {
        min-width: 500px;
    }
</style>
@endsection

@section('content')

<div class="edit-content">
<form action="{{route('song.update', $song->id)}}" method="POST">
{{ csrf_field() }}
{{ method_field('PUT') }}
<div class="mb-3">
  <label for="titleInput" class="form-label">{{ __('ui.name') }}</label>
  <input type="text" class="form-control" id="titleInput" name="name" value="{{$song->name}}">
</div>
<div class="mb-3">
    <label for="artistInput" class="form-label">{{ __('ui.artist') }}</label>
    <input type="text" class="form-control" id="artistInput" name="artist" value="{{$song->artist->name}}" list="artistInputData">
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
  <label for="textInput" class="form-label">{{ __('ui.text') }}</label>
  <textarea class="form-control" id="textInput" name="text" rows="50">{{$song->text}}</textarea>
</div>
<div class="form-check mb-3">
    <input type="hidden" name="hidden_from_guests" value="0">
    <input class="form-check-input" type="checkbox" id="hiddenFromGuests" name="hidden_from_guests" value="1" @checked(old('hidden_from_guests', $song->hidden_from_guests))>
    <label class="form-check-label" for="hiddenFromGuests">{{ __('ui.hide_song_from_guests') }}</label>
    @error('hidden_from_guests') <div class="text-danger" role="alert">{{ $message }}</div> @enderror
</div>
<input type="submit" value="{{ __('ui.save') }}" class="btn btn-primary">
</form>
</div>
@endsection
