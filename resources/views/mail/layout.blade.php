{{-- Plain HTML with inline styles: mail clients strip <link> and most <style>
     blocks, and there is no build step to inline anything for us. --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Brand::dir() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    {{-- RIQX: Vazirmatn from the console for mail apps that load web fonts
         (Apple Mail, iOS, Thunderbird); the rest fall back to Tahoma. --}}
    <style>
        @font-face { font-family: "Vazirmatn"; font-weight: 100 900; src: url("{{ asset('assets/fonts/vazirmatn/Vazirmatn-wght.woff2') }}") format("woff2"); }
        body, td, p, a, span, strong { font-family: Vazirmatn, Tahoma, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; }
    </style>
</head>
<body style="margin:0;padding:0;background:#f4f5f7;font-family:Vazirmatn,Tahoma,-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#26303e;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f5f7;padding:24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                   style="max-width:560px;background:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e3e6ea;">
                <tr>
                    <td style="background:#08132a;border-bottom:3px solid #2563eb;padding:18px 24px;">
                        <span style="color:#ffffff;font-size:18px;font-weight:600;letter-spacing:.2px;">{{ config('app.name') }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px;font-size:15px;line-height:1.7;text-align:{{ \App\Support\Brand::isRtl() ? 'right' : 'left' }};">
                        {{ $slot }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:14px 24px;background:#fafbfc;border-top:1px solid #e3e6ea;font-size:12px;color:#7b8794;">
                        {{ __('Sent by :app at :url.', ['app' => config('app.name'), 'url' => rtrim((string) config('app.url'), '/')]) }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
