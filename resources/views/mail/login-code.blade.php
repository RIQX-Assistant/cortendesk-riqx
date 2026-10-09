@component('mail.layout')
    <p style="margin:0 0 12px;font-size:17px;font-weight:600;">{{ __('Your sign-in code') }}</p>

    <p style="margin:0 0 12px;">{!! __('Someone signed in as :username from a browser this console has not seen before. Enter this code to finish signing in:', ['username' => '<strong style="font-family:ui-monospace,SFMono-Regular,Menlo,monospace;">'.e($username).'</strong>']) !!}</p>

    <p style="margin:20px 0;font-size:32px;font-weight:700;letter-spacing:8px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;">
        {{ $code }}
    </p>

    <p style="margin:0 0 12px;font-size:13px;color:#7b8794;">
        {{ __('The code expires in :minutes minutes.', ['minutes' => $minutes]) }}
        @if ($ip)
            {{ __('The request came from :ip.', ['ip' => $ip]) }}
        @endif
    </p>

    <p style="margin:0;font-size:13px;color:#7b8794;">
        {{ __('If this was not you, someone else knows your password — change it as soon as you can and tell an administrator.') }}
    </p>
@endcomponent
