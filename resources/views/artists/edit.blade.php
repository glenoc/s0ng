@extends('layout')

@section('content')

<div>
<h1>Edit - <span class="h3">{{$artist->name}}</span></h1>
<form action="{{route('artist.update', $artist->id)}}" method="POST">
{{ csrf_field() }}
{{ method_field('PUT') }}
<div class="mb-3">
  <label for="titleInput" class="form-label">Name</label>
  <input type="text" class="form-control" id="titleInput" name="name" value="{{$artist->name}}">
</div>
<input type="submit" class="btn btn-primary">
</form>
<form action="{{route('artist.destroy', $artist->id)}}">
{{ csrf_field() }}
{{ method_field('DELETE') }}
<input type="submit" value="Delete" class="btn btn-outline-danger">
</form>
</div>
@endsection
