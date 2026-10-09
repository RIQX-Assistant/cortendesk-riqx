<div>
    {{-- RIQX: ads shown in the RIQX app (ADS1 / ADS2). --}}
    <div class="card">
        <div class="rd-toolbar">
            <div>
                <h4 class="header-title">{{ __('Ads') }}</h4>
                <p class="rd-card-sub mb-0">
                    {{ __('What the RIQX app shows: ADS1 at the bottom of the left column, ADS2 as a banner above the status bar (also the bottom banner on Android). Several ads in one slot rotate in the order below.') }}
                </p>
            </div>
            <div class="rd-toolbar-actions">
                <button type="button" class="btn btn-outline-light" wire:click="publishNow" @disabled(! $canManage)
                        wire:confirm="{{ __('Tell every RIQX app to fetch the current ads now?') }}">
                    <i class="ri-send-plane-line"></i>{{ __('Publish now') }}
                </button>
                <button type="button" class="btn btn-primary" wire:click="create('ads1')" @disabled(! $canManage)>
                    <i class="ri-add-line"></i>{{ __('Add ad') }}
                </button>
            </div>
        </div>
        <div class="card-body pt-0">
            @if (session('riqx_ads_status'))
                <div class="alert alert-success py-2 mb-3">{{ session('riqx_ads_status') }}</div>
            @endif
            <div class="text-muted fs-13">
                {{ __('Apps check at start, every :minutes minutes and when their window is activated; unchanged ads are not downloaded again.', ['minutes' => (int) ceil(config('riqx.ads_poll_seconds', 600) / 60)]) }}
                @if ($publishedAt)
                    {{ __('Last “publish now”: :time.', ['time' => \App\Support\LocalDate::dateTime($publishedAt)]) }}
                @endif
                <span class="d-block mt-1">{{ __('Feed for the app:') }} <code class="riqx-ltr">{{ $manifestUrl }}</code></span>
            </div>
        </div>
    </div>

    @foreach ($adsBySlot as $slotKey => $ads)
        <div class="card" wire:key="slot-{{ $slotKey }}">
            <div class="rd-toolbar">
                <div>
                    <h5 class="header-title mb-1">
                        {{ $slotKey === 'ads1' ? __('ADS1 — left column') : __('ADS2 — banner above the status bar') }}
                    </h5>
                    <p class="rd-card-sub mb-0">
                        {{ __('Recommended size :w × :h px (:ratio). PNG, JPG, WebP, GIF or MP4/WebM, up to :size MB.', [
                            'w' => $sizes[$slotKey][0], 'h' => $sizes[$slotKey][1],
                            'ratio' => $slotKey === 'ads1' ? '3:4' : '6:1', 'size' => $maxMb,
                        ]) }}
                    </p>
                </div>
                <div class="rd-toolbar-actions">
                    <button type="button" class="btn btn-sm btn-outline-light" wire:click="create('{{ $slotKey }}')" @disabled(! $canManage)>
                        <i class="ri-add-line"></i>{{ __('Add ad') }}
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-centered mb-0">
                    <thead>
                    <tr>
                        <th style="width: 140px;">{{ __('Preview') }}</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Language') }}</th>
                        <th>{{ __('Schedule') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-end">{{ __('Action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($ads as $ad)
                        @php($state = $ad->state())
                        <tr wire:key="ad-{{ $ad->id }}">
                            <td>
                                @if ($ad->media_type === 'video')
                                    <video src="{{ $ad->mediaUrl() }}" muted loop playsinline preload="metadata" class="rounded border" style="max-width: 120px; max-height: 80px;"></video>
                                @else
                                    <img src="{{ $ad->mediaUrl() }}" alt="" class="rounded border" style="max-width: 120px; max-height: 80px; object-fit: contain;">
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $ad->title }}</span>
                                <span class="d-block rd-card-sub">
                                    {{ strtoupper($ad->extension()) }} · {{ number_format($ad->size / 1048576, 1) }} MB
                                    @if ($ad->width) · <span class="riqx-ltr">{{ $ad->width }}×{{ $ad->height }}</span>@endif
                                    @if ($ad->media_type !== 'video') · {{ __(':s s', ['s' => $ad->duration_seconds]) }}@endif
                                </span>
                                @if ($ad->link_url)
                                    <a href="{{ $ad->link_url }}" target="_blank" rel="noopener" class="d-block fs-13 text-truncate riqx-ltr" style="max-width: 260px;">{{ $ad->link_url }}</a>
                                @endif
                            </td>
                            <td>{{ ['all' => __('Both'), 'fa' => 'فارسی', 'en' => 'English'][$ad->locale] ?? $ad->locale }}</td>
                            <td class="fs-13">
                                @if ($ad->starts_at || $ad->ends_at)
                                    {{ __('From') }}: {{ \App\Support\LocalDate::dateTime($ad->starts_at) ?? '—' }}<br>
                                    {{ __('To') }}: {{ \App\Support\LocalDate::dateTime($ad->ends_at) ?? '—' }}
                                @else
                                    <span class="text-muted">{{ __('Always') }}</span>
                                @endif
                            </td>
                            <td>
                                @switch($state)
                                    @case('live') <span class="badge bg-success-subtle text-success">{{ __('Showing') }}</span> @break
                                    @case('scheduled') <span class="badge bg-info-subtle text-info">{{ __('Scheduled') }}</span> @break
                                    @case('ended') <span class="badge bg-secondary-subtle text-secondary">{{ __('Ended') }}</span> @break
                                    @default <span class="badge bg-secondary-subtle text-secondary">{{ __('Off') }}</span>
                                @endswitch
                            </td>
                            <td class="text-end rd-rowact text-nowrap">
                                @if ($canManage)
                                    <a href="javascript:void(0);" class="rd-iconbtn me-1" title="{{ __('Move up') }}" wire:click="move({{ $ad->id }}, 'up')"><i class="ri-arrow-up-line"></i></a>
                                    <a href="javascript:void(0);" class="rd-iconbtn me-2" title="{{ __('Move down') }}" wire:click="move({{ $ad->id }}, 'down')"><i class="ri-arrow-down-line"></i></a>
                                    <a href="javascript:void(0);" class="rd-act me-2" wire:click="toggleActive({{ $ad->id }})">{{ $ad->is_active ? __('Turn off') : __('Turn on') }}</a>
                                    <a href="javascript:void(0);" class="rd-act me-2" wire:click="edit({{ $ad->id }})">{{ __('Edit') }}</a>
                                    <a href="javascript:void(0);" class="text-danger" wire:click="deleteAd({{ $ad->id }})"
                                       wire:confirm="{{ __('Delete this ad? Its file is removed too.') }}">{{ __('Delete') }}</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="rd-empty-cell">
                                <div class="rd-empty">
                                    <div class="rd-empty-icon"><i class="ri-advertisement-line"></i></div>
                                    <p class="rd-empty-title">{{ __('No ads in this slot.') }}</p>
                                    <p class="rd-empty-text">{{ __('With no ad here the app shows nothing in this place.') }}</p>
                                    <button type="button" class="btn btn-sm btn-outline-light" wire:click="create('{{ $slotKey }}')" @disabled(! $canManage)>{{ __('Add ad') }}</button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $editing ? __('Edit ad') : __('Add ad') }}</h5>
                            <button type="button" class="btn-close" wire:click="closeModal" aria-label="{{ __('Close') }}"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label" for="ad-slot">{{ __('Slot') }}</label>
                                <select id="ad-slot" class="form-select" wire:model.live="adSlot">
                                    <option value="ads1">{{ __('ADS1 — left column') }}</option>
                                    <option value="ads2">{{ __('ADS2 — banner above the status bar') }}</option>
                                </select>
                                <div class="form-text">{{ __('Recommended size :w × :h px.', ['w' => $sizes[$adSlot][0] ?? '', 'h' => $sizes[$adSlot][1] ?? '']) }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="ad-file">{{ __('File') }} {!! $editing ? '' : '<span class="text-danger">*</span>' !!}</label>
                                <input type="file" id="ad-file" class="form-control @error('file') is-invalid @enderror" wire:model="file"
                                       accept=".png,.jpg,.jpeg,.webp,.gif,.mp4,.webm">
                                @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">
                                    {{ $editing ? __('Pick a file only to replace the current one.') : '' }}
                                    {{ __('PNG, JPG, WebP, GIF or MP4/WebM, up to :size MB.', ['size' => $maxMb]) }}
                                </div>
                                <div wire:loading wire:target="file" class="form-text">{{ __('Uploading…') }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="ad-title">{{ __('Title') }} <span class="text-danger">*</span></label>
                                <input type="text" id="ad-title" class="form-control @error('title') is-invalid @enderror" wire:model="title" autocomplete="off">
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">{{ __('For the console only; the app does not show it.') }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="ad-link">{{ __('Link') }}</label>
                                <input type="url" id="ad-link" class="form-control riqx-ltr @error('linkUrl') is-invalid @enderror" wire:model="linkUrl" placeholder="https://riqx.one/…">
                                @error('linkUrl') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">{{ __('Opened in the browser when the ad is clicked. Leave empty for no link.') }}</div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <label class="form-label" for="ad-locale">{{ __('Language') }}</label>
                                    <select id="ad-locale" class="form-select" wire:model="locale">
                                        <option value="all">{{ __('Both') }}</option>
                                        <option value="fa">فارسی</option>
                                        <option value="en">English</option>
                                    </select>
                                    <div class="form-text">{{ __('Shown when the app runs in this language.') }}</div>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <label class="form-label" for="ad-duration">{{ __('Display time') }}</label>
                                    <div class="input-group">
                                        <input type="number" min="3" max="300" id="ad-duration" class="form-control @error('durationSeconds') is-invalid @enderror" wire:model="durationSeconds">
                                        <span class="input-group-text">{{ __('seconds') }}</span>
                                    </div>
                                    @error('durationSeconds') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <div class="form-text">{{ __('Images and GIFs; a video plays to its end.') }}</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <label class="form-label" for="ad-start">{{ __('Start') }}</label>
                                    <input type="datetime-local" id="ad-start" class="form-control riqx-ltr @error('startsAt') is-invalid @enderror" wire:model="startsAt">
                                    @error('startsAt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <label class="form-label" for="ad-end">{{ __('End') }}</label>
                                    <input type="datetime-local" id="ad-end" class="form-control riqx-ltr @error('endsAt') is-invalid @enderror" wire:model="endsAt">
                                    @error('endsAt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 form-text mt-n2 mb-3">{{ __('Tehran time, Gregorian calendar. Leave empty to show without a time limit.') }}</div>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="ad-active" wire:model="isActive">
                                <label class="form-check-label" for="ad-active">{{ __('Active') }}</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" wire:click="closeModal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary" wire:target="file" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">{{ $editing ? __('Save Changes') : __('Upload') }}</span>
                                <span wire:loading wire:target="save">{{ __('Saving…') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
