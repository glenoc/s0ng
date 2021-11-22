@extends('layout')

@section('style')
<style>
</style>
@endsection

@section('content')
    <div class="col-12 mt-4">
        <div class="card">
            @can('isSuperAdmin')
            <h1 class="card-header d-flex justify-content-between align-items-center">Users
                <a class="btn btn-primary" href="{{route('users.create')}}">Create</a>
            </h1>
            @endcan
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="users-table">
                        <thead>
                            <tr>
                                @can('isSuperAdmin')<th>#</th>@endcan
                                <th>Username</th>
                                @can('isSuperAdmin')<th></th>@endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                @can('isSuperAdmin')<td>{{$user->id}}</td>@endcan
                                <td>{{$user->username}}</td>
                                @can('isSuperAdmin')
                                <td>
                                    <a class="btn btn-info" href="{{ route('users.edit', [$user->id]) }}">Edit</a>
                                </td>
                                @endcan
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
