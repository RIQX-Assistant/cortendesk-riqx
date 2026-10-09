<div>
    <div class="card">

        {{-- Toolbar --}}
        <div class="rd-toolbar">
            <div class="rd-toolbar-search">
                <div class="input-group">
                    <span class="input-group-text"><i class="ri-search-line"></i></span>
                    <input type="search" class="form-control" placeholder="{{ __('Search device ID, from, path, file name, IP…') }}"
                           wire:model.live.debounce.300ms="search">
                </div>
            </div>
            <input type="date" class="form-control rd-toolbar-filter" wire:model.live="dateFrom" aria-label="{{ __('From date') }}">
            <input type="date" class="form-control rd-toolbar-filter" wire:model.live="dateTo" aria-label="{{ __('To date') }}">
            <select class="form-select rd-toolbar-narrow" wire:model.live="perPage" aria-label="{{ __('Rows per page') }}">
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <div class="rd-toolbar-actions">
                <button type="button" class="btn btn-outline-light" wire:click="resetFilters">{{ __('Reset') }}</button>
                <button type="button" class="btn btn-primary" wire:click="export">
                    <i class="ri-download-2-line"></i>{{ __('Export CSV') }}
                </button>
            </div>
        </div>

        {{-- Desktop table (md and up) --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover table-centered mb-0">
                <thead>
                <tr>
                    <th>{{ __('When') }}</th>
                    <th>{{ __('Device') }}</th>
                    <th>{{ __('From') }}</th>
                    <th>{{ __('Direction') }}</th>
                    <th>{{ __('Path') }}</th>
                    <th>{{ __('Files') }}</th>
                    <th>IP</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($transfers as $transfer)
                    <tr wire:key="t{{ $transfer->id }}">
                        <td>
                            <span title="{{ \App\Support\LocalDate::dateTimeSeconds($transfer->created_at) }}">{{ $transfer->created_at?->diffForHumans() }}</span>
                        </td>
                        <td><span class="fw-semibold">{{ $transfer->rustdesk_id }}</span></td>
                        <td>
                            <span class="rd-cell-title">{{ $transfer->from_peer ?: '—' }}</span>
                            <span class="rd-cell-sub">{{ $transfer->from_name }}</span>
                        </td>
                        <td>
                            @if ($transfer->direction === 1)
                                <span class="badge bg-warning-subtle text-warning"><i class="ri-arrow-down-line me-1"></i>{{ __('Receive') }}</span>
                            @else
                                <span class="badge bg-info-subtle text-info"><i class="ri-arrow-up-line me-1"></i>{{ __('Send') }}</span>
                            @endif
                        </td>
                        <td>
                            @if ($transfer->isClipboard())
                                <span class="badge bg-secondary-subtle text-secondary me-1" title="{{ __('Copied and pasted through the clipboard') }}">{{ __('Clipboard') }}</span>
                            @endif
                            <span class="d-inline-block text-truncate align-middle rd-mono" style="max-width: 240px;"
                                  title="{{ $transfer->isClipboard() ? implode(', ', $transfer->fileNames()) : $transfer->path }}">{{ $transfer->pathLabel() ?: '—' }}</span>
                        </td>
                        <td>{{ $transfer->file_count }}</td>
                        <td class="rd-mono">{{ $transfer->ip ?: '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="rd-empty-cell">
                            <div class="rd-empty">
                                <div class="rd-empty-icon"><i class="ri-file-transfer-line"></i></div>
                                <p class="rd-empty-title">{{ __('No file transfers match your filters.') }}</p>
                                <p class="rd-empty-text">{{ __('Files moved during a remote session are recorded here.') }}</p>
                                <button type="button" class="btn btn-sm btn-outline-light" wire:click="resetFilters">{{ __('Clear filters') }}</button>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile card list (below md) --}}
        <div class="d-md-none rd-cardlist">
            @forelse ($transfers as $transfer)
                <div class="rd-mini" wire:key="mt{{ $transfer->id }}">
                    <div class="rd-mini-head">
                        <div class="min-width-0">
                            <span class="rd-mini-title">{{ $transfer->rustdesk_id }}</span>
                            <span class="rd-mini-sub">
                                {{ __('from :peer', ['peer' => $transfer->from_peer ?: '—']) }}@if ($transfer->from_name) ({{ $transfer->from_name }})@endif
                            </span>
                        </div>
                        @if ($transfer->direction === 1)
                            <span class="badge bg-warning-subtle text-warning"><i class="ri-arrow-down-line me-1"></i>{{ __('Receive') }}</span>
                        @else
                            <span class="badge bg-info-subtle text-info"><i class="ri-arrow-up-line me-1"></i>{{ __('Send') }}</span>
                        @endif
                    </div>
                    <div class="mt-2">
                        <small class="d-block text-truncate rd-mono" title="{{ $transfer->isClipboard() ? implode(', ', $transfer->fileNames()) : $transfer->path }}">
                            @if ($transfer->isClipboard())
                                <span class="badge bg-secondary-subtle text-secondary me-1">{{ __('Clipboard') }}</span>
                            @else
                                <i class="ri-file-line me-1"></i>
                            @endif
                            {{ $transfer->pathLabel() ?: '—' }}
                        </small>
                        <span class="rd-mini-sub">
                            {{ trans_choice(':count file|:count files', $transfer->file_count, ['count' => $transfer->file_count]) }} ·
                            {{ $transfer->ip ?: '—' }} ·
                            {{ $transfer->created_at?->diffForHumans(short: true) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="rd-empty">
                    <div class="rd-empty-icon"><i class="ri-file-transfer-line"></i></div>
                    <p class="rd-empty-title">{{ __('No file transfers match your filters.') }}</p>
                    <p class="rd-empty-text">{{ __('Files moved during a remote session are recorded here.') }}</p>
                    <button type="button" class="btn btn-sm btn-outline-light" wire:click="resetFilters">{{ __('Clear filters') }}</button>
                </div>
            @endforelse
        </div>

        <div class="rd-tablefoot">
            <span>{{ __('Showing :from–:to of :total', ['from' => $transfers->firstItem() ?? 0, 'to' => $transfers->lastItem() ?? 0, 'total' => $transfers->total()]) }}</span>
            {{ $transfers->links() }}
        </div>
    </div>
</div>
