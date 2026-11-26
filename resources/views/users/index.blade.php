@extends('layout')

@section('content')
<div class="card mt-2">
    <div class="card-header d-flex justify-content-between align-items-center gap-2">
        <h1 class="h2 card-title mb-0">{{ __('ui.users') }}</h1>
        @can('isSuperAdmin')
            <a class="btn btn-outline-success" href="{{ route('users.create') }}">{{ __('ui.create') }}</a>
        @endcan
    </div>
    <div class="card-body">
        <ul class="list-unstyled mb-0">
            @foreach ($users as $user)
                <li class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                    <span class="text-break">
                        {{ $user->username }}
                        @can('isSuperAdmin')
                            <span class="badge rounded-pill text-bg-secondary">#{{ $user->id }}</span>
                        @endcan
                    </span>
                    @can('isSuperAdmin')
                        <a class="btn btn-outline-secondary" href="{{ route('users.edit', $user->id) }}">{{ __('ui.edit') }}</a>
                    @endcan
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
