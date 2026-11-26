@extends('layout')

@section('content')
<div class="card mt-2">
    <div class="card-header">
        <h1 class="h2 card-title mb-0">{{ __('ui.edit') }} <small>{{ $artist->name }}</small></h1>
    </div>
    <div class="card-body">
        <form action="{{ route('artist.update', $artist->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="titleInput" class="form-label">{{ __('ui.name') }}</label>
                <input type="text" id="titleInput" name="name" value="{{ old('name', $artist->name) }}"
                    @class(['form-control', 'is-invalid' => $errors->has('name')])
                    @if ($errors->has('name')) aria-invalid="true" aria-describedby="artist-name-error" @endif>
                @error('name')
                    <div id="artist-name-error" class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">{{ __('ui.save') }}</button>
        </form>
        <form class="mt-2" action="{{ route('artist.destroy', $artist->id) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">{{ __('ui.delete') }}</button>
        </form>
    </div>
</div>
@endsection
