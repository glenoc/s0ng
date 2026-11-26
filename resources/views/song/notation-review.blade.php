@extends('layout')

@section('content')
<div class="card mt-2">
    <div class="card-header">
        <h1 class="h2 card-title mb-0">{{ __('ui.notation_review') }}</h1>
    </div>
    <div class="card-body">
        <p>{{ __('ui.notation_review_help') }}</p>
        <noscript><p class="text-danger">{{ __('ui.notation_review_requires_javascript') }}</p></noscript>
        <div id="notation-review"
            data-unknown="{{ __('ui.notation_review_unknown') }}"
            data-no-chords="{{ __('ui.no_song_chords') }}"
            data-capo-error="{{ __('ui.capo_out_of_range') }}"
            data-count="{{ __('ui.notation_review_count') }}"
            data-empty="{{ __('ui.notation_review_empty') }}">
            <p role="status">{{ __('ui.notation_review_loading') }}</p>
            <ul class="list-unstyled mb-0">
                @foreach ($songs as $song)
                    <li class="border-top py-3" data-notation="{{ json_encode($song->chordNotation()) }}" hidden>
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <a href="{{ route('song.show', $song) }}">{{ $song->name }} - {{ $song->artist->name ?? '' }}</a>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('song.edit', $song) }}">{{ __('ui.edit') }}</a>
                        </div>
                        <ul class="notation-review-reasons mt-2 mb-0"></ul>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
