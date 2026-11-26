<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>S0ng</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=PT+Mono&display=swap" rel="stylesheet">


        @vite(['resources/sass/app.scss', 'resources/js/app.js'])

        <!-- Styles -->
        <style>
            /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}a{background-color:transparent}[hidden]{display:none}html{font-family:system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;line-height:1.5}*,:after,:before{box-sizing:border-box;border:0 solid #e2e8f0}a{color:inherit;text-decoration:inherit}svg,video{display:block;vertical-align:middle}video{max-width:100%;height:auto}.bg-white{--bg-opacity:1;background-color:#fff;background-color:rgba(255,255,255,var(--bg-opacity))}.bg-gray-100{--bg-opacity:1;background-color:#f7fafc;background-color:rgba(247,250,252,var(--bg-opacity))}.border-gray-200{--border-opacity:1;border-color:#edf2f7;border-color:rgba(237,242,247,var(--border-opacity))}.border-t{border-top-width:1px}.flex{display:flex}.grid{display:grid}.hidden{display:none}.items-center{align-items:center}.justify-center{justify-content:center}.font-semibold{font-weight:600}.h-5{height:1.25rem}.h-8{height:2rem}.h-16{height:4rem}.text-sm{font-size:.875rem}.text-lg{font-size:1.125rem}.leading-7{line-height:1.75rem}.mx-auto{margin-left:auto;margin-right:auto}.ml-1{margin-left:.25rem}.mt-2{margin-top:.5rem}.mr-2{margin-right:.5rem}.ml-2{margin-left:.5rem}.mt-4{margin-top:1rem}.ml-4{margin-left:1rem}.mt-8{margin-top:2rem}.ml-12{margin-left:3rem}.-mt-px{margin-top:-1px}.max-w-6xl{max-width:72rem}.min-h-screen{min-height:100vh}.overflow-hidden{overflow:hidden}.p-6{padding:1.5rem}.py-4{padding-top:1rem;padding-bottom:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.pt-8{padding-top:2rem}.fixed{position:fixed}.relative{position:relative}.top-0{top:0}.right-0{right:0}.shadow{box-shadow:0 1px 3px 0 rgba(0,0,0,.1),0 1px 2px 0 rgba(0,0,0,.06)}.text-center{text-align:center}.text-gray-200{--text-opacity:1;color:#edf2f7;color:rgba(237,242,247,var(--text-opacity))}.text-gray-300{--text-opacity:1;color:#e2e8f0;color:rgba(226,232,240,var(--text-opacity))}.text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.text-gray-500{--text-opacity:1;color:#a0aec0;color:rgba(160,174,192,var(--text-opacity))}.text-gray-600{--text-opacity:1;color:#718096;color:rgba(113,128,150,var(--text-opacity))}.text-gray-700{--text-opacity:1;color:#4a5568;color:rgba(74,85,104,var(--text-opacity))}.text-gray-900{--text-opacity:1;color:#1a202c;color:rgba(26,32,44,var(--text-opacity))}.underline{text-decoration:underline}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.w-5{width:1.25rem}.w-8{width:2rem}.w-auto{width:auto}.grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}@media (min-width:640px){.sm\:rounded-lg{border-radius:.5rem}.sm\:block{display:block}.sm\:items-center{align-items:center}.sm\:justify-start{justify-content:flex-start}.sm\:justify-between{justify-content:space-between}.sm\:h-20{height:5rem}.sm\:ml-0{margin-left:0}.sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}.sm\:pt-0{padding-top:0}.sm\:text-left{text-align:left}.sm\:text-right{text-align:right}}@media (min-width:768px){.md\:border-t-0{border-top-width:0}.md\:border-l{border-left-width:1px}.md\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (min-width:1024px){.lg\:px-8{padding-left:2rem;padding-right:2rem}}@media (prefers-color-scheme:dark){.dark\:bg-gray-800{--bg-opacity:1;background-color:#2d3748;background-color:rgba(45,55,72,var(--bg-opacity))}.dark\:bg-gray-900{--bg-opacity:1;background-color:#1a202c;background-color:rgba(26,32,44,var(--bg-opacity))}.dark\:border-gray-700{--border-opacity:1;border-color:#4a5568;border-color:rgba(74,85,104,var(--border-opacity))}.dark\:text-white{--text-opacity:1;color:#fff;color:rgba(255,255,255,var(--text-opacity))}.dark\:text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.dark\:text-gray-500{--tw-text-opacity:1;color:#6b7280;color:rgba(107,114,128,var(--tw-text-opacity))}}}
        </style>

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>

        @yield('style')
    </head>
    <body data-theme="normal">
        <nav class="navbar navbar-expand-lg navbar-dark song-navbar">
            <div class="container">
                <a class="navbar-brand song-brand" href="{{ route('home') }}">
                    <span class="song-brand-mark" aria-hidden="true">♪</span>
                    <span>S0ng</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#songNavbar" aria-controls="songNavbar" aria-expanded="false" aria-label="{{ __('ui.open_menu') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="songNavbar">
                    <ul class="navbar-nav me-lg-auto mb-3 mb-lg-0">
                        <li class="nav-item">
                            <a @class(['nav-link', 'active' => request()->routeIs('song.*')]) href="{{ route('song.index') }}">{{ __('ui.songs') }}</a>
                        </li>
                        <li class="nav-item">
                            <a @class(['nav-link', 'active' => request()->routeIs('artist.*')]) href="{{ route('artist.index') }}">{{ __('ui.artists') }}</a>
                        </li>
                    </ul>
                    <form class="song-search" action="{{ route('song.search') }}" role="search">
                        <label class="visually-hidden" for="song-search">{{ __('ui.search_songbook') }}</label>
                        <input id="song-search" class="form-control" type="search" name="search" placeholder="{{ __('ui.find_song') }}" aria-label="{{ __('ui.search') }}">
                        <button class="btn song-search-button" type="submit">{{ __('ui.search') }}</button>
                    </form>
                    <div class="song-nav-actions">
                        <label class="visually-hidden" for="theme-select">{{ __('ui.select_theme') }}</label>
                        <select id="theme-select" class="form-select song-theme-select" aria-label="{{ __('ui.select_theme') }}" data-christmas="{{ __('ui.christmas') }}" data-christmas-season="{{ __('ui.christmas_season') }}">
                            <option value="normal">{{ __('ui.normal') }}</option>
                            <option value="dark">{{ __('ui.dark') }}</option>
                            <option value="christmas" disabled>{{ __('ui.christmas_season') }}</option>
                        </select>
                        <button id="snow-toggle" class="btn btn-snow-toggle" type="button" aria-pressed="false" data-snow-on="{{ __('ui.snow_on') }}" data-snow-off="{{ __('ui.snow_off') }}" data-snow-season="{{ __('ui.snow_season') }}">
                            {{ __('ui.snow_off') }}
                        </button>
                        @auth
                            <a class="btn song-auth-button" href="{{ route('users.edit', auth()->user()) }}">{{ __('ui.my_profile') }}</a>
                            <a class="btn song-auth-button" href="{{ route('logout') }}">{{ __('ui.log_out') }}</a>
                        @else
                            <a class="btn song-auth-button" href="{{ route('login') }}">{{ __('ui.log_in') }}</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
        @auth
            <div id="song-playability-config" hidden
                data-chords="{{ json_encode(auth()->user()->known_chords ?? []) }}"
                data-no-chords="{{ __('ui.enter_chords') }}"
                data-playable="{{ __('ui.playable') }}"
                data-with-capo="{{ __('ui.playable_with_capo') }}"
                data-unplayable="{{ __('ui.unplayable') }}"
                data-unknown="{{ __('ui.playability_unknown') }}"></div>
        @endauth
        <div id="snow-effect" aria-hidden="true"></div>
        <button id="snow-shovel" class="snow-shovel" type="button" aria-label="{{ __('ui.snow_shovel') }}" aria-describedby="snow-shovel-help" hidden>
            <svg viewBox="0 0 52 72" aria-hidden="true">
                <defs>
                    <linearGradient id="shovel-handle" x1="0" y1="0" x2="1" y2="0">
                        <stop stop-color="#a96b35"/>
                        <stop offset=".45" stop-color="#f0c482"/>
                        <stop offset="1" stop-color="#b7793e"/>
                    </linearGradient>
                    <linearGradient id="shovel-blade" x1="0" y1="0" x2="1" y2="1">
                        <stop stop-color="#ff5144"/>
                        <stop offset=".5" stop-color="#e61516"/>
                        <stop offset="1" stop-color="#a90d10"/>
                    </linearGradient>
                </defs>
                <path d="M24 13h4v35h-4z" fill="url(#shovel-handle)" stroke="#a76b35" stroke-width=".5"/>
                <path d="M16 2h20l-1 5q-1 6-6 8h-6q-5-2-6-8zm4 3 .6 2q.6 3 3.4 4.5h4q2.8-1.5 3.4-4.5l.6-2z" fill="url(#shovel-blade)" fill-rule="evenodd" stroke="#9e1010" stroke-width="1" stroke-linejoin="round"/>
                <path d="M18 3h16" stroke="#ff9a89" stroke-width=".8" stroke-linecap="round"/>
                <path d="M10 48h32q3 0 4 4l3 14q.5 4-3 4H6q-3.5 0-3-4l3-14q1-4 4-4z" fill="url(#shovel-blade)" stroke="#971010" stroke-width="1.5"/>
                <path d="M10 51h32M11 54 9 65m9-11-1 11m17-11 1 11m6-11 2 11" fill="none" stroke="#ff8d78" stroke-width="1.2" stroke-linecap="round" opacity=".8"/>
                <path d="M22 46h8v12q0 3-4 5-4-2-4-5z" fill="#c41013" stroke="#a40d11" stroke-width=".8"/>
                <circle cx="26" cy="50" r="1" fill="#e1e4dc"/>
                <circle cx="26" cy="54" r=".8" fill="#e1e4dc"/>
                <path d="M6 68h40" stroke="#ff9b83" stroke-width="1.2" stroke-linecap="round" opacity=".8"/>
            </svg>
        </button>
        <div
            id="song-start-notifications"
            class="container position-fixed top-0 start-50 translate-middle-x mt-3"
            style="z-index: 1080; pointer-events: none;"
            data-poll-url="{{ route('song-starts.poll') }}"
            data-latest-id="{{ \App\Models\SongStart::max('id') ?? 0 }}"
            data-song-started="{{ __('ui.song_started') }}"
            data-open-song="{{ __('ui.open_song') }}"
            data-close-notification="{{ __('ui.close_notification') }}"
            aria-live="polite"
            aria-atomic="false"
        ></div>
        <div class="@yield('content-container-class', 'container')">
            <div class="row justify-content-center @yield('content-row-class')">
                <div class="col-12">
                    @yield('content')
                </div>
            </div>
        </div>
    </body>
    @yield('script')
</html>
