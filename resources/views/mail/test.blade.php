@component('mail.layout')
    <p style="margin:0 0 12px;font-size:17px;font-weight:600;">{{ __('Your mail settings work.') }}</p>
    <p style="margin:0 0 12px;">{{ __('This is a test message from the :app console. If you are reading it, the SMTP relay accepted the message and delivered it.', ['app' => config('app.name')]) }}</p>
    <p style="margin:0;color:#7b8794;font-size:13px;">{{ __('Nothing else to do — you can close this.') }}</p>
@endcomponent
