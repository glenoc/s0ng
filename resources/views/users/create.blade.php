@extends('layout')

@section('style')
<style>
</style>
@endsection

@section('content')
<div class="row justify-content-center mx-0">
    <div class="mt-3 px-2">
        <div class="card">
            <div class="card-header">
                <h1>Create user</h1>
            </div>
            <div class="card-body py-2">
                <form class="form-horizontal" id="create-user-form" method="POST"
                    action="{{ route('users.store')}}">
                    {{ csrf_field() }}
                    {{ method_field('POST') }}
                    <div class="form-group row my-2 {{ $errors->has('username') ? ' has-error' : '' }}">
                        <label for="username" class="col-md-4 control-label">Username</label>

                        <div class="col-md-6">
                            <input id="username" type="text" class="form-control" name="username"
                                    value="{{ old('username', '') }}" autofocus>
                            @if ($errors->has('username'))
                                <div class="help-block">
                                    <strong>{{ $errors->first('username') }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row my-2{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="password" class="col-md-4 control-label">Password</label>

                        <div class="col-md-6">
                            <input id="password" type="password" class="form-control" name="password">

                            @if ($errors->has('password'))
                                <div class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row my-2">
                        <label for="password-confirm" class="col-md-4 control-label">Confirm password</label>

                        <div class="col-md-6">
                            <input id="password-confirm" type="password" class="form-control"
                                    name="password_confirmation">
                        </div>
                    </div>
                    <div class="row button-row my-2">
                        <div class="col-sm-6">
                        </div>
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-primary float-right" id="create-user-btn">
                                Create
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    $(function(){

    });
</script>
@endsection
