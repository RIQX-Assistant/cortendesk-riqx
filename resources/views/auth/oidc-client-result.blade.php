@extends('layouts.guest')

@section('title', $ok ? __('Signed In') : __('Sign-In Failed'))

@section('content')
    <div class="card">

        <div class="card-header rd-auth-head py-3 text-center d-flex align-items-center justify-content-center">
            <a href="{{ url('/') }}" class="rd-auth-brand mb-0" dir="ltr">
                <img src="{{ \App\Support\Brand::url('mark.png') }}" alt="{{ config('app.name') }}" width="60" height="60" class="rd-auth-brand-logo">
                <span class="rd-auth-brand-wordmark">{{ config('app.name') }}</span>
            </a>
        </div>

        <div class="card-body p-4 text-center">
            <span class="rd-auth-icon {{ $ok ? 'rd-tone-green' : 'rd-tone-red' }}">
                <i class="{{ $ok ? 'ri-checkbox-circle-line' : 'ri-error-warning-line' }}"></i>
            </span>

            <h4 class="rd-auth-title">
                {{ $ok ? __('Signed in to RustDesk') : __('Sign-in failed') }}
            </h4>

            <p class="rd-auth-sub mb-0">{{ $message }}</p>

            @unless ($ok)
                <p class="rd-auth-foot-note mt-3">
                    {{ __('Return to the RustDesk client and try again, or contact your administrator.') }}
                </p>
            @endunless
        </div>
    </div>
@endsection
