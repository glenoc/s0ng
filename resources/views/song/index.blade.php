<ul>
@foreach ($songs as $song)
<li>{{$song->name}} - {{$song->artist->name??''}}</li>
@endforeach
</ul>