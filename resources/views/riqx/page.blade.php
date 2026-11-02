{{-- RIQX: the public /privacy and /Help pages, in the look of riqx.one (docs/13-public-pages.md). --}}
@php
    $rdPage = __('riqx_pages.'.$page);
    $rdUi = __('riqx_pages.ui');
    $rdText = fn (string $text) => strtr($text, [
        ':downloads' => route('downloads.index'),
        ':privacy' => route('riqx.privacy'),
        ':help' => route('riqx.help'),
        ':source_app' => config('riqx.client_source_url'),
        ':source_panel' => config('riqx.source_url'),
    ]);
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Brand::dir() }}" data-theme-default="auto">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="theme-color" content="#050b18">
<title>{{ $rdPage['title'] }} | RIQX</title>
<meta name="description" content="{{ $rdPage['intro'] }}">
<link rel="icon" type="image/png" href="{{ \App\Support\Brand::url('favicon.png') }}">
<link href="{{ \App\Support\Asset::url('assets/fonts/vazirmatn/vazirmatn.css') }}" rel="stylesheet">
<link href="{{ \App\Support\Asset::url('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
<link href="{{ \App\Support\Asset::url('assets/css/riqx-site.css') }}" rel="stylesheet">
<script src="{{ \App\Support\Asset::url('assets/js/riqx-site.js') }}"></script>
</head>
<body>
<a class="skip" href="#content">{{ $rdUi['skip'] }}</a>

<div class="cosmos">
<div class="cosmos-bg" aria-hidden="true"><canvas class="sky"></canvas></div>

<header class="topbar" id="top">
    <a class="wordmark" href="{{ config('riqx.site_url') }}" dir="ltr" aria-label="{{ $rdUi['site'] }}">RIQ<span>X</span></a>
    <p class="topbar-tag">{{ $rdUi['tag'] }}</p>
    <nav class="topnav" aria-label="{{ $rdUi['links'] }}">
        <a href="{{ route('riqx.privacy') }}" @if ($page === 'privacy') aria-current="page" @endif>{{ $rdUi['privacy'] }}</a>
        <a href="{{ route('riqx.help') }}" @if ($page === 'help') aria-current="page" @endif>{{ $rdUi['help'] }}</a>
        <div class="lang-switch" role="group" aria-label="{{ __('Language') }}">
            @foreach (config('riqx.locales') as $rdCode => $rdName)
                <a href="{{ route('riqx.locale', $rdCode) }}" lang="{{ $rdCode }}" title="{{ $rdName }}" @class(['on' => app()->getLocale() === $rdCode])>{{ $rdCode === 'fa' ? 'فا' : 'EN' }}</a>
            @endforeach
        </div>
        <div class="theme-switch" data-mode="auto" data-dark="{{ $rdUi['dark'] }}" data-light="{{ $rdUi['light'] }}">
            <button class="ts-toggle" type="button" role="switch" aria-checked="true" aria-label="{{ $rdUi['dark'] }}">
                <span class="ts-track" aria-hidden="true">
                    <i class="ts-star s1"></i><i class="ts-star s2"></i><i class="ts-star s3"></i>
                    <i class="ts-cloud c1"></i><i class="ts-cloud c2"></i>
                </span>
                <span class="ts-knob" aria-hidden="true"><i class="ts-crater k1"></i><i class="ts-crater k2"></i><i class="ts-crater k3"></i></span>
            </button>
            <button class="ts-auto" type="button" aria-pressed="true" title="{{ $rdUi['auto_title'] }}">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="4.6"/><path d="M12 7.4a4.6 4.6 0 0 0 0 9.2z" fill="currentColor" stroke="none"/><path d="M12 2.6v2M12 19.4v2M2.6 12h2M19.4 12h2M5.35 5.35l1.41 1.41M17.24 17.24l1.41 1.41M18.65 5.35l-1.41 1.41M6.76 17.24l-1.41 1.41"/></svg>
                <span>{{ $rdUi['auto'] }}</span>
            </button>
        </div>
        <a class="topnav-cta" href="{{ route('downloads.index') }}">{{ $rdUi['downloads'] }}</a>
    </nav>
</header>

<main id="content">
    <section class="page-hero">
        <p class="eyebrow" dir="ltr">{{ $rdPage['eyebrow'] }}</p>
        <h1>{{ $rdPage['heading'] }}</h1>
        <p class="page-sub">{{ $rdPage['intro'] }}</p>
        @if ($page === 'privacy')
            <p class="page-date"><i class="ri-calendar-line" aria-hidden="true"></i> {{ $rdUi['updated'] }}</p>
        @endif
    </section>

    <section class="glance" aria-label="{{ $rdPage['title'] }}">
        @foreach ($rdPage['highlights'] as $rdItem)
            @php($rdTag = isset($rdItem['href']) ? 'a' : 'div')
            <{{ $rdTag }} class="glance-card reveal" style="--c:{{ $rdItem['c'] }};--i:{{ $loop->index }}" @isset($rdItem['href']) href="{{ $rdItem['href'] }}" @endisset>
                <span class="glance-icon"><i class="{{ $rdItem['icon'] }}" aria-hidden="true"></i></span>
                <b>{{ $rdItem['title'] }}</b>
                <span>{{ $rdItem['text'] }}</span>
            </{{ $rdTag }}>
        @endforeach
    </section>

    <div class="doc">
        <nav class="toc" aria-labelledby="toc-title">
            <p id="toc-title">{{ $rdUi['contents'] }}</p>
            @foreach ($rdPage['sections'] as $rdSection)
                <a href="#{{ $rdSection['id'] }}">{{ $rdSection['title'] }}</a>
            @endforeach
            <a href="#contact">{{ $rdUi['contact'] }}</a>
        </nav>

        <div class="doc-body">
            @foreach ($rdPage['sections'] as $rdSection)
                <article class="doc-card reveal" id="{{ $rdSection['id'] }}" style="--c:{{ $rdSection['c'] }}">
                    <h2><span class="doc-icon"><i class="{{ $rdSection['icon'] }}" aria-hidden="true"></i></span>{{ $rdSection['title'] }}</h2>
                    @foreach ($rdSection['body'] as $rdBlock)
                        @if (is_string($rdBlock))
                            <p>{!! $rdText($rdBlock) !!}</p>
                        @elseif (isset($rdBlock['h']))
                            <h3>{{ $rdBlock['h'] }}</h3>
                        @elseif (isset($rdBlock['ul']))
                            <ul>@foreach ($rdBlock['ul'] as $rdLine)<li>{!! $rdText($rdLine) !!}</li>@endforeach</ul>
                        @elseif (isset($rdBlock['ol']))
                            <ol>@foreach ($rdBlock['ol'] as $rdLine)<li>{!! $rdText($rdLine) !!}</li>@endforeach</ol>
                        @elseif (isset($rdBlock['note']))
                            <p class="doc-note"><i class="ri-information-line" aria-hidden="true"></i><span>{!! $rdText($rdBlock['note']) !!}</span></p>
                        @elseif (isset($rdBlock['qa']))
                            <div class="faq-list">
                                @foreach ($rdBlock['qa'] as [$rdQuestion, $rdAnswer])
                                    <details><summary>{{ $rdQuestion }}</summary><p>{!! $rdText($rdAnswer) !!}</p></details>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </article>
            @endforeach
        </div>
    </div>
</main>
</div>

<a class="to-top" href="#top" aria-label="{{ $rdUi['to_top'] }}">
    <svg class="to-top-ring" viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="21"/><circle class="to-top-prog" cx="24" cy="24" r="21"/></svg>
    <svg class="to-top-arrow" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
</a>

<footer class="footer">
    <div class="footer-grid">
        <section class="f-brand">
            <a class="wordmark" href="{{ config('riqx.site_url') }}" dir="ltr" aria-label="{{ $rdUi['site'] }}">RIQ<span>X</span></a>
            <p>{{ $rdUi['about'] }}</p>
            <ul class="meaning" aria-label="RIQX">
                @foreach ([['R', 'Research', '#4fa3ff'], ['I', 'International', '#9b8cff'], ['Q', 'Quality', '#2fd4a8'], ['X', 'Expertise', '#ffb547']] as [$rdLetter, $rdWord, $rdColor])
                    <li style="--i:{{ $loop->index }};--mc:{{ $rdColor }}"><b>{{ $rdLetter }}</b><span lang="en">{{ $rdWord }}</span>@if (app()->getLocale() !== 'en')<small>{{ $rdUi['meaning'][$loop->index] }}</small>@endif</li>
                @endforeach
            </ul>
        </section>
        <section class="f-contact" id="contact" aria-labelledby="f-contact-t">
            <h2 id="f-contact-t">{{ $rdUi['contact'] }}</h2>
            <dl>
                <div><dt>{{ $rdUi['address_label'] }}</dt><dd class="addr">{{ $rdUi['address'] }}</dd></div>
                <div><dt>{{ $rdUi['postal_label'] }}</dt><dd><span class="lat" dir="ltr">5614776475</span></dd></div>
                <div><dt>{{ $rdUi['phone_label'] }}</dt><dd><a class="lat" href="tel:04532282575" dir="ltr">045-32282575</a></dd></div>
            </dl>
        </section>
        <nav class="f-services" aria-labelledby="f-links-t">
            <h2 id="f-links-t">{{ $rdUi['links'] }}</h2>
            <a href="{{ route('riqx.privacy') }}">{{ $rdUi['privacy'] }}</a>
            <a href="{{ route('riqx.help') }}">{{ $rdUi['help'] }}</a>
            <a href="{{ route('downloads.index') }}">{{ $rdUi['downloads'] }}</a>
            <a href="{{ config('riqx.site_url') }}">{{ $rdUi['site'] }}</a>
        </nav>
    </div>
    <div class="footer-bottom">
        <p>{{ $rdUi['rights'] }} <span dir="ltr">{{ $rdUi['year'] }}</span></p>
        <nav class="socials" aria-label="RIQX">
            <a href="https://eitaa.com/RIQXOne" target="_blank" rel="noopener noreferrer">{{ $rdUi['eitaa'] }}</a>
            <a href="https://t.me/RIQXOne" target="_blank" rel="noopener noreferrer">{{ $rdUi['telegram'] }}</a>
            <a href="https://instagram.com/RIQXOne" target="_blank" rel="noopener noreferrer">{{ $rdUi['instagram'] }}</a>
        </nav>
    </div>
</footer>
</body>
</html>
