@extends('layout')

@section('content')

<div>
<form action="{{route('artist.store')}}" method="POST">
{{ csrf_field() }}
<div class="mb-3">
  <label for="titleInput" class="form-label">Name</label>
  <input type="text" class="form-control" id="titleInput" name="name">
</div>
<input type="submit" class="btn btn-primary">
<!-- <div class="mb-3">
  <label for="textInput" class="form-label">Text</label>
  <textarea class="form-control" id="textInput" rows="50"></textarea>
</div> -->
</form>
</div>
@endsection