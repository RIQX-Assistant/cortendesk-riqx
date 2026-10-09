<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Brand::dir() }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8"/>
    <title>@yield('title', __('Sign In')) | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="{{ \App\Support\Asset::url('assets/js/theme-init.js') }}"></script>
    @include('layouts.partials.riqx-head')
</head>

<body class="rd-auth-bg position-relative">

    <div class="rd-auth-page pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5">

                    @yield('content')

                    <div class="text-center mt-3">
                        @include('layouts.partials.riqx-lang-switch')
                        {{-- The AGPL source link is in the console footer, for signed-in users. --}}
                        <p class="rd-auth-note mt-2 riqx-credit">{{ __('Based on') }} <a href="{{ config('riqx.site_url') }}" target="_blank" rel="noopener">RIQX</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ \App\Support\Asset::url('assets/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ \App\Support\Asset::url('assets/js/shell.js') }}"></script>
</body>
</html>
