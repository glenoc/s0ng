@auth
    @php($savedTransposition = $songTranspositions->get($song->id)?->pivot)
    <span class="badge rounded-pill song-playability" hidden
        data-notation="{{ json_encode($song->chordNotation()) }}"
        data-semitones="{{ $savedTransposition->semitones ?? 0 }}"
        data-capo="{{ $savedTransposition->capo ?? '' }}"
        title="{{ __('ui.playability_help') }}"></span>
@endauth
