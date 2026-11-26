@extends('layout')

@section('content')
<div class="card mt-2">
    <div class="card-header">
        <h1 class="h2 card-title mb-0">{{ __('ui.create_user') }}</h1>
    </div>
    <div class="card-body">
        <form id="create-user-form" method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label">{{ __('ui.username') }}</label>
                <input id="username" type="text" name="username" value="{{ old('username', '') }}"
                    @class(['form-control', 'is-invalid' => $errors->has('username')])
                    @if ($errors->has('username')) aria-invalid="true" aria-describedby="username-error" @endif
                    autocomplete="username" autofocus>
                @error('username')
                    <div id="username-error" class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">{{ __('ui.password') }}</label>
                <input id="password" type="password" name="password"
                    @class(['form-control', 'is-invalid' => $errors->has('password')])
                    @if ($errors->has('password')) aria-invalid="true" aria-describedby="password-error" @endif
                    autocomplete="new-password">
                @error('password')
                    <div id="password-error" class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password-confirm" class="form-label">{{ __('ui.confirm_password') }}</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary" id="create-user-btn">{{ __('ui.create') }}</button>
        </form>
    </div>
</div>
@endsection
