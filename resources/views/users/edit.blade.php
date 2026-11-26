@extends('layout')

@section('content')
<div class="card mt-2">
    <div class="card-header">
        <h1 class="h2 card-title mb-0">{{ __('ui.edit_user') }} <small>{{ $user->username }}</small></h1>
    </div>
    <div class="card-body">
        <form id="edit-user-form" method="POST" action="{{ route('users.update', $user->id) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="username" class="form-label">{{ __('ui.username') }}</label>
                <input id="username" type="text" name="username" value="{{ old('username', $user->username) }}"
                    @class(['form-control', 'is-invalid' => $errors->has('username')])
                    @if ($errors->has('username')) aria-invalid="true" aria-describedby="username-error" @endif
                    autocomplete="username" autofocus>
                @error('username')
                    <div id="username-error" class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#change-password-subform"
                    aria-expanded="{{ $errors->has('password') ? 'true' : 'false' }}" aria-controls="change-password-subform">
                    {{ __('ui.change_password') }}
                </button>
            </div>
            <div id="change-password-subform" @class(['collapse', 'show' => $errors->has('password')])>
                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('ui.new_password') }}</label>
                    <input id="password" type="password" name="password"
                        @class(['form-control', 'is-invalid' => $errors->has('password')])
                        @if ($errors->has('password')) aria-invalid="true" aria-describedby="password-error" @endif
                        autocomplete="new-password">
                    @error('password')
                        <div id="password-error" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password-confirm" class="form-label">{{ __('ui.confirm_new_password') }}</label>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary" id="save-user-btn">{{ __('ui.save') }}</button>
                @if (!$isMe)
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        {{ __('ui.delete') }}
                    </button>
                @endif
            </div>
        </form>
        @if ($isMe)
            <hr>
            <form id="known-chords-form" method="POST" action="{{ route('profile.chords.update') }}"
                data-known-chords="{{ json_encode(is_array(old('known_chords')) ? old('known_chords') : ($user->known_chords ?? [])) }}"
                data-song-notation="{{ json_encode($songNotation) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <fieldset id="known-chords-fieldset" hidden aria-describedby="known-chords-help">
                        <legend class="fs-6">{{ __('ui.known_chords') }}</legend>
                        <div id="known-chords-options" class="d-flex flex-wrap gap-2"></div>
                        <details id="more-chords" class="mt-3">
                            <summary>{{ __('ui.more_chords') }} <span id="more-chords-selected"></span></summary>
                            <div id="more-chords-options" class="d-flex flex-wrap gap-2 mt-2"></div>
                        </details>
                        <button class="btn btn-primary mt-3" type="submit">{{ __('ui.save_chords') }}</button>
                    </fieldset>
                    <div id="known-chords-help" class="form-text">{{ __('ui.known_chords_help') }}</div>
                    @if ($errors->has('known_chords') || $errors->has('known_chords.*'))
                        <div id="known-chords-error" class="text-danger" role="alert">{{ $errors->first('known_chords') ?: $errors->first('known_chords.*') }}</div>
                    @endif
                    <noscript><p class="text-danger">{{ __('ui.chords_require_javascript') }}</p></noscript>
                </div>
                @if (session('chords_saved'))
                    <p class="text-success" role="status">{{ __('ui.chords_saved') }}</p>
                @endif
            </form>
        @endif
    </div>
</div>

@if (!$isMe)
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalTitle" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="deleteModalTitle">{{ __('ui.warning') }}</h2>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal" aria-label="{{ __('ui.close') }}">&times;</button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">{!! __('ui.confirm_delete_user', ['username' => e($user->username)]) !!}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('ui.close') }}</button>
                    <form method="POST" action="{{ route('users.destroy', $user) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ __('ui.delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
