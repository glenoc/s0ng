 <!DOCTYPE html>
 <html>
 <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>S0ng</title>

    <!-- Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

 </head>
 <body data-theme="normal">
    <nav class="navbar navbar-dark song-navbar">
        <div class="container">
            <a class="navbar-brand song-brand" href="{{ route('home') }}">
                <span class="song-brand-mark" aria-hidden="true">♪</span>
                <span>S0ng</span>
            </a>
            <label class="visually-hidden" for="theme-select">Vælg tema</label>
            <select id="theme-select" class="form-select song-theme-select ms-auto" aria-label="Vælg tema">
                <option value="normal">Normal</option>
                <option value="dark">Dark</option>
                <option value="christmas" disabled>Jul (15/11-31/12)</option>
            </select>
        </div>
    </nav>
    <div class="container">
        <div class="card card-default mt-4">
            <div class="card-header">
                <span class="h1">Login</span>
            </div>
            <div class="card-body">
                <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                    {{ csrf_field() }}

                    <div class="form-group row my-2  {{ $errors->has('username') ? ' has-error' : '' }}">
                        <label for="username" class="col-md-4 control-label">Username</label>

                        <div class="col-md-6">
                            <input id="username" type="username" class="form-control" name="username" value="{{ old('username') }}" required autofocus>

                            @if ($errors->has('username'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('username') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row my-2 {{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="password" class="col-md-4 control-label">Password</label>

                        <div class="col-md-6">
                            <input id="password" type="password" class="form-control" name="password" required>

                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row my-2">
                        <div class="col-md-8 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                Login
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
