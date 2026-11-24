@extends('layout')

@section('style')
<style>
</style>
@endsection

@section('content')
    <div class="mt-3 px-2">
        <div class="card">
            <div class="card-header">
                <h1>{{ __('ui.edit_user') }} <small>{{ $user->username }}</small></h1>
            </div>
            <div class="card-body py-2">
                <form class="form-horizontal" id="edit-user-form" method="POST"
                    action="{{ route('users.update', [$user->id])}}">
                    {{ csrf_field() }}
                    @method('PUT')

                    <div class="form-group row{{ $errors->has('username') ? ' has-error' : '' }}">
                        <label for="username" class="col-md-4 control-label">{{ __('ui.username') }}</label>

                        <div class="col-md-6">
                            <input id="username" type="text" class="form-control" name="username"
                                    value="{{ old('username', $user->username) }}" autofocus>
                            @if ($errors->has('username'))
                                <div class="help-block">
                                    <strong>{{ $errors->first('username') }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <a class="btn btn-info" data-bs-toggle="collapse" href="#change-password-subform" role="button" aria-expanded="false" aria-controls="change-password-subform">
                                {{ __('ui.change_password') }}
                            </a>
                        </div>
                    </div>
                    <div id="change-password-subform" class="collapse {{ $errors->has('password') ? ' show' : '' }}">
                        <div class="form-group row {{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">{{ __('ui.new_password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password">

                                @if ($errors->has('password'))
                                    <div class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 control-label">{{ __('ui.confirm_new_password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control"
                                        name="password_confirmation">
                            </div>
                        </div>
                    </div>
                    <div class="row button-row">
                        <div class="col-sm-6 my-3">
                            @if(!$isMe)
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                {{ __('ui.delete') }}
                            </button>
                            @endif
                        </div>
                        <div class="col-sm-6 my-3">
                            <button type="submit" class="btn btn-primary float-right" id="save-user-btn">
                                {{ __('ui.save') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
</div>
<!-- Modal -->
<div class="modal fade" id="deleteModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content card-warning">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('ui.warning') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('ui.close') }}">&times;</button>
            </div>
            <div class="modal-body">
                <p>{!! __('ui.confirm_delete_user', ['username' => e($user->username)]) !!}</p>
            </div>
            <div class="modal-footer">
                <form style="display: inline-block" method="post" action="{{route('users.destroy', [$user])}}">
                    {{ method_field('DELETE')}}
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-danger">{{ __('ui.delete') }}</button>
                </form>
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">{{ __('ui.close') }}</button>
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
