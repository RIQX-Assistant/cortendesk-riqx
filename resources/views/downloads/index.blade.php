@extends('layouts.guest')

@section('title', __('Client Downloads'))

@section('content')
    <div class="card">

        <div class="card-header rd-auth-head py-3 text-center d-flex align-items-center justify-content-center">
            <a href="{{ url('/') }}" class="rd-auth-brand mb-0" dir="ltr">
                <img src="{{ \App\Support\Brand::url('mark.png') }}" alt="{{ config('app.name') }}"width="60" height="60" class="rd-auth-brand-logo">
                <span class="rd-auth-brand-wordmark">{{ config('app.name') }}</span>
            </a>
        </div>

        <div class="card-body p-4">

            <div class="text-center mb-4">
                <h4 class="rd-auth-title">{{ __('Client Downloads') }}</h4>
                <p class="rd-auth-sub">
                    {{ __('Installers for this server, pre-configured to connect on first run.') }}
                </p>
            </div>

            @if ($downloads->isEmpty())
                <div class="rd-empty">
                    <div class="rd-empty-icon"><i class="ri-download-cloud-line"></i></div>
                    <p class="rd-empty-title">{{ __('No client builds published yet.') }}</p>
                    <p class="rd-empty-text">{{ __('An administrator uploads them under System → Client Downloads.') }}</p>
                </div>
            @else
                @foreach ($downloads->groupBy('platform') as $platform => $group)
                    <p class="rd-dl-group">
                        <x-platform-icon :platform="$platform" size="fs-16" class="me-1" />
                        {{ __(\App\Support\ClientPlatform::label($platform)) }}
                    </p>
                    <x-client-download-links :downloads="$group" class="mb-3" />
                @endforeach

                <p class="rd-auth-note mb-0 text-center">
                    {{ __('Only download these from a link you trust. Check with whoever supports your machines if you were not expecting this page.') }}
                </p>
            @endif

            <div class="text-center mt-3">
                <a href="{{ route('login') }}" class="rd-auth-quiet">{{ __('Console sign-in') }}</a>
            </div>
        </div>
    </div>
@endsection
