{{-- RIQX: replaces Laravel's error page shell (401–503) so error pages carry
     the brand, the language direction and Vazirmatn instead of system fonts. --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Brand::dir() }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8"/>
    <title>@yield('title') | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="{{ \App\Support\Asset::url('assets/js/theme-init.js') }}"></script>
    @include('layouts.partials.riqx-head')
</head>
<body class="rd-auth-bg position-relative">
    <div class="rd-auth-page pt-5 pb-5 position-relative">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5">
                    <div class="card">
                        <div class="card-header rd-auth-head py-3 text-center d-flex align-items-center justify-content-center">
                            <a href="{{ url('/') }}" class="rd-auth-brand mb-0" dir="ltr">
                                <img src="{{ \App\Support\Brand::url('mark.png') }}" alt="{{ config('app.name') }}" width="60" height="60" class="rd-auth-brand-logo">
                                <span class="rd-auth-brand-wordmark">{{ config('app.name') }}</span>
                            </a>
                        </div>
                        <div class="card-body p-4 text-center">
                            <div class="display-4 fw-bold mb-2" dir="ltr">@yield('code')</div>
                            <div class="rd-auth-title mb-4">@yield('message')</div>
                            <a href="{{ url('/') }}" class="btn btn-primary">{{ __('Back to the console') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
