@extends('layout')

@section('content-container-class', 'container-lg song-show-container')
@section('content-row-class', 'gx-0')

@section('style')
<style>
    .song-text {
        font-family: 'PT Mono', monospace;
        white-space: pre;
        display: block;
    }

    @media only screen and (max-width: 991.98px) {
        .song-show-container {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }
    }

    @media only screen and (max-width: 600px) {
        #song-lyrics {
            overflow-x: auto;
        }
    }

    .chord-line {
        font-weight: 1000;
    }

    .guest-chord-line[hidden] {
        display: none;
    }

    .transpose-control {
        width: 8rem;
    }

    .transpose-control .btn {
        width: 2.5rem;
    }

    .song-autoscroll {
        position: sticky;
        top: 0;
        z-index: 1020;
        background: var(--bs-body-bg);
        border-bottom: 1px solid var(--bs-border-color);
    }

    #songdiv {
        padding-bottom: 50vh;
    }
</style>
@endsection

@section('content')

<div id="songdiv">
    <div class="card mt-1">
        <div class="card-header p-1">
            <h1 class="mb-1">{{$song->name}}</h1>
            <h3 class="mb-1">{{$song->artist->name}}</h3>
            @auth
                @if ($song->hidden_from_guests)
                    <span class="badge text-bg-secondary">{{ __('ui.song_hidden_from_guests') }}</span>
                @endif
                <form class="d-inline" method="POST" action="{{ route('song-starts.store', $song) }}">
                    @csrf
                    <button class="btn btn-outline-primary" type="submit">{{ __('ui.start_song') }}</button>
                </form>
                <a class="btn btn-outline-secondary" href="{{route('song.edit', $song->id)}}">{{ __('ui.edit') }}</a>
                <form id="song-transposition" class="mt-2" method="POST" action="{{ route('song.transposition', $song) }}"
                    data-semitones="{{ $transposition->semitones ?? 0 }}"
                    data-capo="{{ $transposition->capo ?? '' }}"
                    data-capo-error="{{ __('ui.capo_out_of_range') }}">
                    @csrf
                    @if ($errors->has('semitones') || $errors->has('capo'))
                        <div class="text-danger" role="alert">
                            {{ $errors->first('semitones') }} {{ $errors->first('capo') }}
                        </div>
                    @endif
                    <fieldset hidden>
                        <div class="d-flex flex-wrap align-items-end gap-2">
                            <div>
                                <div id="semitones-label" class="form-label mb-0">{{ __('ui.transpose') }}</div>
                                <div class="input-group transpose-control">
                                    <button class="btn btn-outline-secondary" type="button" data-adjust="semitones" data-step="-1" aria-label="{{ __('ui.transpose_down') }}">−</button>
                                    <output id="song-semitones-value" class="form-control text-center" aria-labelledby="semitones-label" aria-live="polite"></output>
                                    <button class="btn btn-outline-secondary" type="button" data-adjust="semitones" data-step="1" aria-label="{{ __('ui.transpose_up') }}">+</button>
                                </div>
                                <input id="song-semitones" type="hidden" name="semitones">
                            </div>
                            <div>
                                <div id="capo-label" class="form-label mb-0">{{ __('ui.capo_fret') }}</div>
                                <div class="input-group transpose-control">
                                    <button class="btn btn-outline-secondary" type="button" data-adjust="capo" data-step="-1" aria-label="{{ __('ui.capo_down') }}">−</button>
                                    <output id="song-capo-value" class="form-control text-center" aria-labelledby="capo-label" aria-live="polite"></output>
                                    <button class="btn btn-outline-secondary" type="button" data-adjust="capo" data-step="1" aria-label="{{ __('ui.capo_up') }}">+</button>
                                </div>
                                <input id="song-capo" type="hidden" name="capo">
                            </div>
                            <button class="btn btn-primary" type="submit">{{ __('ui.save_transposition') }}</button>
                            <button id="song-transposition-reset" class="btn btn-outline-secondary" type="button">{{ __('ui.reset_transposition') }}</button>
                        </div>
                        <small>{{ __('ui.transposition_help') }}</small>
                        <div id="song-transposition-summary" aria-live="polite"
                            data-summary="{{ __('ui.transposition_summary') }}"></div>
                    </fieldset>
                </form>
                <div id="song-chord-overview" class="mt-2" aria-live="polite"
                    data-empty="{{ __('ui.no_song_chords') }}"
                    data-unknown="{{ __('ui.some_chords_unknown') }}">
                    <strong>{{ __('ui.song_chords') }}:</strong> <span></span>
                </div>
                <div id="song-chord-diagram" class="mt-2" hidden aria-live="polite"
                    data-title="{{ __('ui.chord_diagram_title') }}"
                    data-description="{{ __('ui.chord_diagram_description') }}"
                    data-open="{{ __('ui.chord_diagram_open') }}"
                    data-unavailable="{{ __('ui.chord_diagram_unavailable') }}"
                    data-fret="{{ __('ui.chord_diagram_fret') }}"
                    data-help="{{ __('ui.chord_diagram_help') }}"></div>
            @endauth
        </div>
        @auth
            <div id="song-autoscroll" class="song-autoscroll p-2" hidden
                data-save-url="{{ route('song.autoscroll', $song) }}"
                data-start="{{ __('ui.autoscroll_start') }}"
                data-stop="{{ __('ui.autoscroll_stop') }}"
                data-saving="{{ __('ui.autoscroll_saving') }}"
                data-saved="{{ __('ui.autoscroll_saved') }}"
                data-error="{{ __('ui.autoscroll_error') }}"
                data-wake-error="{{ __('ui.autoscroll_wake_error') }}">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button id="autoscroll-toggle" class="btn btn-sm btn-outline-secondary" type="button" aria-pressed="false">{{ __('ui.autoscroll_start') }}</button>
                    <label for="autoscroll-speed">{{ __('ui.autoscroll_speed') }}</label>
                    <input id="autoscroll-speed" type="range" min="1" max="25" step="1" value="{{ $autoscrollSpeed ?? 8 }}">
                    <output id="autoscroll-speed-value" for="autoscroll-speed"></output>
                    <button id="autoscroll-retry" class="btn btn-sm btn-outline-danger" type="button" hidden>{{ __('ui.autoscroll_retry') }}</button>
                </div>
                <div id="autoscroll-status" role="status" aria-live="polite"></div>
                <div id="autoscroll-wake-status" role="status" aria-live="polite"></div>
            </div>
        @endauth
        <div class="card-body p-1">
            <div id="song-lyrics">
                @foreach ($lines as $line)
                    <span @class(['song-text', 'chord-line' => $line->isChord, 'text-line' => !$line->isChord, 'guest-chord-line' => $line->isChord && auth()->guest()]) @if ($line->isChord && auth()->guest()) hidden @endif>{{$line->text}}</span>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
