<!DOCTYPE html>
@php($rdTheme = auth()->user()?->themePreference() ?? 'dark')
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Brand::dir() }}" data-bs-theme="{{ $rdTheme === 'light' ? 'light' : 'dark' }}" data-rd-theme-pref="{{ $rdTheme }}" data-rd-sidebar="expanded">
<head>
    <meta charset="utf-8"/>
    <title>@yield('title', __('Console')) | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ __('RIQX remote desktop console') }}"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Theme + sidebar state before first paint (no flash) -->
    <script src="{{ \App\Support\Asset::url('assets/js/theme-init.js') }}"></script>

    @include('layouts.partials.riqx-head')
</head>

<body>
    <div class="rd-app">

        @include('layouts.partials.topbar')

        @include('layouts.partials.sidebar')

        <div class="rd-main">
            <div class="rd-content">
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-12">
                            <div class="rd-page-head">
                                <div class="rd-page-crumbs">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="{{ route('overview') }}">{{ config('app.name') }}</a></li>
                                        @hasSection('subtitle')
                                            <li class="breadcrumb-item"><a href="javascript:void(0);">@yield('subtitle')</a></li>
                                        @endif
                                        <li class="breadcrumb-item active">@yield('title')</li>
                                    </ol>
                                </div>
                                <h4 class="rd-page-title">@yield('title')</h4>
                            </div>
                        </div>
                    </div>

                    @yield('content')

                </div>
            </div>

            <footer class="rd-footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            © {{ config('app.name') }}
                        </div>
                        <div class="col-md-6">
                            {{-- AGPL-3.0 §13: the source link must stay. --}}
                            <div class="text-md-end d-none d-md-block riqx-credit">
                                <span class="text-muted">{{ __('Based on') }} <a href="{{ config('riqx.site_url') }}" target="_blank" rel="noopener">RIQX</a> · <a href="{{ config('riqx.source_url') }}" target="_blank" rel="noopener">{{ __('Source code') }}</a></span>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

    </div>

    <script>window.riqxI18n = {copied: @js(__('Copied')), pressCtrlC: @js(__('Press Ctrl+C to copy'))};</script>
    <script src="{{ \App\Support\Asset::url('assets/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ \App\Support\Asset::url('assets/js/shell.js') }}"></script>
    <script src="{{ \App\Support\Asset::url('assets/js/cortendesk.js') }}"></script>

    @stack('scripts')
</body>
</html>
