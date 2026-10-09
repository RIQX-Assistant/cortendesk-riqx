{{-- RIQX: language switch (topbar and sign-in pages). Short labels on phones. --}}
<div class="riqx-lang-switch" role="group" aria-label="{{ __('Language') }}">
    @foreach (config('riqx.locales') as $rdCode => $rdName)
        <a href="{{ route('riqx.locale', $rdCode) }}" lang="{{ $rdCode }}" title="{{ $rdName }}" @class(['active' => app()->getLocale() === $rdCode])><span class="d-none d-sm-inline">{{ $rdName }}</span><span class="d-sm-none">{{ $rdCode === 'fa' ? 'فا' : strtoupper($rdCode) }}</span></a>
    @endforeach
</div>
