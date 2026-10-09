@component('mail.layout')
    <p style="margin:0 0 12px;font-size:17px;font-weight:600;">{{ __('Reset your password') }}</p>

    <p style="margin:0 0 12px;">{!! __('Someone asked to reset the password for :username on :app. Use the link below to choose a new one:', ['username' => '<strong style="font-family:ui-monospace,SFMono-Regular,Menlo,monospace;">'.e($user->username).'</strong>', 'app' => e(config('app.name'))]) !!}</p>

    <p style="margin:20px 0;">
        <a href="{{ $resetUrl }}"
           style="display:inline-block;padding:11px 20px;background:#f26c23;color:#ffffff;text-decoration:none;border-radius:5px;font-weight:600;">
            {{ __('Choose a new password') }}
        </a>
    </p>

    <p style="margin:0 0 12px;font-size:13px;color:#7b8794;">
        {{ __('The link expires in :minutes minutes and can be used once.', ['minutes' => $ttlMinutes]) }}
        @if ($requestedIp)
            {{ __('The request came from :ip.', ['ip' => $requestedIp]) }}
        @endif
    </p>

    <p style="margin:0 0 12px;font-size:13px;color:#7b8794;">
        {{ __('If the button does not work, paste this into your browser:') }}<br>
        <span style="word-break:break-all;">{{ $resetUrl }}</span>
    </p>

    <p style="margin:0;font-size:13px;color:#7b8794;">
        {{ __('If you did not ask for this, ignore this message — your password has not changed.') }}
    </p>
@endcomponent
