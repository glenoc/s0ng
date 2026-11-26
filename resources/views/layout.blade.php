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
                        @can('isSuperAdmin')
                            <li class="nav-item">
                                <a @class(['nav-link', 'active' => request()->routeIs('admin.song-notation')]) href="{{ route('admin.song-notation') }}">{{ __('ui.notation_review') }}</a>
                            </li>
                        @endcan
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
                        <button id="snow-toggle" class="btn btn-snow-toggle" type="button" aria-pressed="false" data-snow-on="{{ __('ui.snow_on') }}" data-snow-off="{{ __('ui.snow_off') }}" data-snow-season="{{ __('ui.snow_season') }}" hidden>
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
        <div id="snow-game" class="snow-game" hidden
            data-authenticated="{{ auth()->check() ? 'true' : 'false' }}"
            data-small="{{ __('ui.snow_tool_small') }}"
            data-large="{{ __('ui.snow_tool_large') }}"
            data-scraper="{{ __('ui.snow_tool_scraper') }}"
            data-plow="{{ __('ui.snow_tool_plow') }}"
            data-blower="{{ __('ui.snow_tool_blower') }}"
            data-next="{{ __('ui.snow_game_next') }}"
            data-unlocked="{{ __('ui.snow_game_unlocked') }}">
            <details id="snow-game-details" open>
                <summary>{{ __('ui.snow_game_progress') }}</summary>
                <strong id="snow-game-tool"></strong>
                <div id="snow-game-progress"></div>
                <progress id="snow-game-meter" aria-labelledby="snow-game-progress"></progress>
            </details>
        </div>
        <span id="snow-shovel-help" class="visually-hidden">{{ __('ui.snow_game_help') }}</span>
        <span id="snow-game-announcement" class="visually-hidden" aria-live="polite"></span>
        <button id="snow-shovel" class="snow-shovel" type="button" aria-describedby="snow-shovel-help" hidden>
            <svg data-tool="small" viewBox="0 0 52 72" aria-hidden="true">
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
            <svg data-tool="large" viewBox="0 0 72 90" aria-hidden="true" hidden>
                <path d="M33 15h6v45h-6z" fill="#b7793e" stroke="#70421d" stroke-width="2"/>
                <path d="M24 3h24l-3 10H27z" fill="#2563eb" stroke="#1e3a8a" stroke-width="2"/>
                <path d="M7 56h58l-3 22q-1 8-9 8H19q-8 0-9-8z" fill="#2563eb" stroke="#1e3a8a" stroke-width="3"/>
                <path d="M20 61l2 19m14-19v20m16-20-2 19" stroke="#bfdbfe" stroke-width="2"/>
            </svg>
            <svg data-tool="scraper" viewBox="0 0 110 86" aria-hidden="true" hidden>
                <path d="M39 54 28 8h54L71 54M37 9h36" fill="none" stroke="#536779" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M5 54h100l-6 22q-1 5-7 5H18q-6 0-7-5z" fill="#df2020" stroke="#991b1b" stroke-width="3"/>
                <path d="M13 75h84" stroke="#fca5a5" stroke-width="3"/>
            </svg>
            <svg data-tool="plow" viewBox="0 0 180 110" aria-hidden="true" hidden>
                <path d="M41 69h78v17H35z" fill="#263b2b"/>
                <path d="M27 67q3-16 21-16h22v18z" fill="#338b3b" stroke="#174f22" stroke-width="3"/>
                <path d="M82 51h37q12 0 16 11l2 12H79z" fill="#3ba34a" stroke="#174f22" stroke-width="3"/>
                <path d="M87 56h28m-29 5h31" stroke="#9ad68d" stroke-width="2"/>
                <path d="M58 48h18l3 5H56zM56 48V37h7v11" fill="#242b2c" stroke="#162b1c" stroke-width="2"/>
                <path d="M88 51l-4-13m-8 0h14" fill="none" stroke="#293d31" stroke-width="3" stroke-linecap="round"/>
                <circle cx="130" cy="65" r="3" fill="#fff3a0"/>
                <path d="M133 79h18m-2-26 8 2-1 33-11-3z" fill="#667880" stroke="#34444a" stroke-width="3"/>
                <path d="M151 51q9 5 25 5l-3 27q-11 0-25-3z" fill="#f4b51b" stroke="#936500" stroke-width="3"/>
                <path d="M152 76q12 5 21 4" fill="none" stroke="#ffe081" stroke-width="2"/>
                <circle cx="48" cy="83" r="21" fill="#242c2e" stroke="#101617" stroke-width="3"/>
                <circle cx="48" cy="83" r="12" fill="#e1ba2c" stroke="#96751a" stroke-width="3"/>
                <circle cx="48" cy="83" r="4" fill="#6f812b"/>
                <circle cx="113" cy="88" r="15" fill="#242c2e" stroke="#101617" stroke-width="3"/>
                <circle cx="113" cy="88" r="8" fill="#e1ba2c" stroke="#96751a" stroke-width="2"/>
            </svg>
            <svg data-tool="blower" viewBox="0 0 200 110" aria-hidden="true" hidden>
                <path d="M35 69h96v16H29z" fill="#243540"/>
                <path d="M39 61V21l9-9h44l8 49z" fill="#2a6390" stroke="#173d5c" stroke-width="3"/>
                <path d="M46 26l5-7h14v30H46zM70 19h17l6 30H70z" fill="#bce3f2" stroke="#20455f" stroke-width="2"/>
                <path d="M69 14v46M45 55h51" fill="none" stroke="#173d5c" stroke-width="3"/>
                <path d="M96 53h29q11 0 14 12l2 12H96z" fill="#3889be" stroke="#173d5c" stroke-width="3"/>
                <path d="M105 59h23m-23 5h25m-24 5h23" stroke="#174461" stroke-width="2"/>
                <path d="M26 56h13m1-35h-9v14" fill="none" stroke="#263b46" stroke-width="4"/>
                <circle cx="134" cy="60" r="3" fill="#fff3a0"/>
                <circle cx="48" cy="80" r="26" fill="#242c2e" stroke="#101617" stroke-width="3"/>
                <circle cx="48" cy="80" r="14" fill="#c6d0d5" stroke="#6b7981" stroke-width="3"/>
                <circle cx="48" cy="80" r="5" fill="#75858b"/>
                <circle cx="114" cy="86" r="17" fill="#242c2e" stroke="#101617" stroke-width="3"/>
                <circle cx="114" cy="86" r="9" fill="#c6d0d5" stroke="#6b7981" stroke-width="2"/>
                <path d="M134 79h12" stroke="#4b5c63" stroke-width="5"/>
                <path d="M146 64h45l-3 33h-44z" fill="#f19d12" stroke="#935a08" stroke-width="3"/>
                <g class="snow-auger" fill="none" stroke="#fff0b0" stroke-width="3">
                    <circle cx="168" cy="80" r="11"/>
                    <path d="M168 69v22m-11-11h22"/>
                </g>
                <path d="M159 64V47q0-7 7-7h4l10-10 7 7-10 10h-9v17z" fill="#f19d12" stroke="#935a08" stroke-width="3" stroke-linejoin="round"/>
                <g class="snow-spray" fill="#f5fbff" stroke="#a9d7ec" stroke-width="1">
                    <g class="snow-spray-light">
                        <circle cx="183" cy="33" r="4"/>
                        <circle cx="184" cy="34" r="3"/>
                        <circle cx="182" cy="32" r="3.5"/>
                        <circle cx="183" cy="34" r="2.5"/>
                        <circle cx="182" cy="33" r="3"/>
                        <circle cx="184" cy="32" r="2.5"/>
                    </g>
                    <g class="snow-spray-medium">
                        <circle cx="183" cy="33" r="4.5"/>
                        <circle cx="184" cy="34" r="3.5"/>
                        <circle cx="182" cy="32" r="4"/>
                        <circle cx="183" cy="34" r="3"/>
                        <circle cx="182" cy="33" r="3.5"/>
                        <circle cx="184" cy="32" r="3"/>
                    </g>
                    <g class="snow-spray-heavy">
                        <circle cx="183" cy="33" r="5"/>
                        <circle cx="184" cy="34" r="4"/>
                        <circle cx="182" cy="32" r="4.5"/>
                        <circle cx="183" cy="34" r="3.5"/>
                        <circle cx="182" cy="33" r="4"/>
                        <circle cx="184" cy="32" r="3.5"/>
                    </g>
                </g>
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
