<div wire:poll.15s>

    {{-- Summary chips. Buttons, not badges (issue #26): each one filters the
         list underneath, and the active one is marked. Pending only appears
         when something is actually waiting. --}}
    <div class="rd-chiprow">
        <button type="button" class="rd-chip rd-chip-btn rd-tone-blue @if(! $trashed && ! $pendingTab && $status === 'all') rd-chip-active @endif"
                wire:click="filterByChip('all')" title="{{ __('Show every device') }}">
            <i class="ri-computer-line rd-chip-icon"></i>
            <span class="rd-chip-value">{{ $totalCount }}</span>
            <span class="rd-chip-label">{{ __('Devices') }}</span>
        </button>
        <button type="button" class="rd-chip rd-chip-btn rd-tone-green @if(! $trashed && ! $pendingTab && $status === 'online') rd-chip-active @endif"
                wire:click="filterByChip('online')" title="{{ __('Only devices online now') }}">
            <span class="rd-chip-dot"></span>
            <span class="rd-chip-value">{{ $onlineCount }}</span>
            <span class="rd-chip-label">{{ __('Online') }}</span>
        </button>
        <button type="button" class="rd-chip rd-chip-btn rd-tone-muted @if(! $trashed && ! $pendingTab && $status === 'offline') rd-chip-active @endif"
                wire:click="filterByChip('offline')" title="{{ __('Only devices currently offline') }}">
            <span class="rd-chip-dot"></span>
            <span class="rd-chip-value">{{ $totalCount - $onlineCount }}</span>
            <span class="rd-chip-label">{{ __('Offline') }}</span>
        </button>
        @if ($duplicates && ($duplicates->count() > 0 || $status === 'duplicates'))
            <button type="button" class="rd-chip rd-chip-btn rd-tone-amber @if(! $trashed && ! $pendingTab && $status === 'duplicates') rd-chip-active @endif"
                    wire:click="filterByChip('duplicates')" title="{{ __('Devices that look like another device under a new ID') }}">
                <i class="ri-file-copy-2-line rd-chip-icon"></i>
                <span class="rd-chip-value">{{ $duplicates->count() }}</span>
                <span class="rd-chip-label">{{ __('Possible duplicates') }}</span>
            </button>
        @endif
        @if ($pendingCount > 0)
            <button type="button" class="rd-chip rd-chip-btn rd-tone-amber @if($pendingTab) rd-chip-active @endif"
                    wire:click="openPending" title="{{ __('Devices waiting for approval') }}">
                <i class="ri-time-line rd-chip-icon"></i>
                <span class="rd-chip-value">{{ $pendingCount }}</span>
                <span class="rd-chip-label">{{ __('Pending') }}</span>
            </button>
        @endif
    </div>

    <div class="card">

        {{-- Toolbar --}}
        <div class="rd-toolbar">
            <div class="rd-toolbar-search">
                <div class="input-group">
                    <span class="input-group-text"><i class="ri-search-line"></i></span>
                    <input type="search" class="form-control" placeholder="{{ __('Search ID, alias, hostname, user, IP…') }}"
                           wire:model.live.debounce.300ms="search">
                </div>
            </div>
            <select class="form-select rd-toolbar-filter" wire:model.live="status" @disabled($trashed)>
                <option value="all">{{ __('All statuses') }}</option>
                <option value="online">{{ __('Online') }}</option>
                <option value="offline">{{ __('Offline') }}</option>
                <option value="duplicates">{{ __('Duplicates') }}</option>
            </select>
            <select class="form-select rd-toolbar-filter" wire:model.live="group">
                <option value="0">{{ __('All groups') }}</option>
                @foreach ($groups as $g)
                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                @endforeach
            </select>
            @if (auth()->user()?->is_admin)
                <select class="form-select rd-toolbar-filter" wire:model.live="owner">
                    <option value="0">{{ __('All owners') }}</option>
                    <option value="-1">{{ __('Unassigned') }}</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}">{{ $u->username }}</option>
                    @endforeach
                </select>
            @endif
            {{--
                Sorting on a phone: the card list has no headings to click, so
                the picker and the reverse arrow stand in for them. Options come
                from the component's own allowlist so the two cannot drift.
            --}}
            <div class="rd-sort-mobile d-md-none">
                <select class="form-select rd-toolbar-filter" aria-label="{{ __('Sort devices by') }}"
                        wire:change="selectSort($event.target.value)">
                    @foreach (\App\Livewire\DeviceList::SORTABLE as $key => $unused)
                        @continue($key === 'owner' && ! auth()->user()?->is_admin)
                        <option value="{{ $key }}" @selected($sortField === $key)>
                            {{ __('Sort: :column', ['column' => $key === 'id' ? 'ID' : __(\App\Livewire\DeviceList::COLUMNS[$key] ?? 'Status')]) }}
                        </option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-outline-light" wire:click="sortBy('{{ $sortField }}')"
                        aria-label="{{ __('Reverse sort order') }}" title="{{ __('Reverse sort order') }}">
                    <i class="{{ $sortDirection === 'asc' ? 'ri-sort-asc' : 'ri-sort-desc' }}"></i>
                </button>
            </div>
            <div class="rd-toolbar-actions">
                <button type="button" class="btn btn-outline-light" wire:click="resetFilters">{{ __('Reset') }}</button>
                @if ($trashed)
                    <button type="button" class="btn btn-light" wire:click="$set('trashed', false)">
                        <i class="ri-arrow-left-line"></i>{{ __('Back to Devices') }}
                    </button>
                @elseif (auth()->user()?->consoleAllows('device', 'rw'))
                    <button type="button" class="btn btn-primary" wire:click="create">
                        <i class="ri-add-line"></i>{{ __('Add Device') }}
                    </button>
                @endif
            </div>
        </div>

        @unless ($trashed)
            <div class="rd-toolbar">
                <a href="javascript:void(0);" class="fs-13 text-muted d-none d-md-inline"
                   wire:click="$toggle('columnsOpen')">
                    <i class="ri-layout-column-line me-1"></i>{{ __('Columns') }}
                </a>
                <a href="javascript:void(0);" class="fs-13 text-muted" wire:click="exportCsv"
                   title="{{ __('Download the current view as CSV') }}">
                    <i class="ri-download-2-line me-1"></i>{{ __('Export CSV') }}
                </a>
                @if ($pendingCount > 0)
                    <a href="javascript:void(0);" class="fs-13 text-warning fw-semibold" wire:click="$set('pendingTab', true)">
                        <i class="ri-time-line me-1"></i>{{ __('Pending approval') }}
                        <span class="badge bg-warning-subtle text-warning ms-1">{{ $pendingCount }}</span>
                    </a>
                @endif
                <a href="javascript:void(0);" class="fs-13 text-muted ms-auto" wire:click="$set('trashed', true)">
                    <i class="ri-delete-bin-line me-1"></i>{{ __('Recycle Bin (:count)', ['count' => $trashedCount]) }}
                </a>
            </div>

            {{-- Column picker (issue #16). A plain inline panel, not a Bootstrap
                 dropdown: Livewire re-renders on every checkbox and a JS-owned
                 dropdown would snap shut after each click. Desktop-only, like
                 the table it configures — the mobile cards have a fixed layout. --}}
            @if ($columnsOpen)
                <div class="rd-toolbar d-none d-md-flex">
                    <div class="rd-inset w-100">
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            @foreach (\App\Livewire\DeviceList::COLUMNS as $key => $label)
                                @continue ($key === 'owner' && ! auth()->user()?->is_admin)
                                <div class="form-check form-check-inline m-0">
                                    <input type="checkbox" class="form-check-input" id="col-{{ $key }}"
                                           value="{{ $key }}" wire:model.live="columns">
                                    <label class="form-check-label fs-13" for="col-{{ $key }}">{{ __($label) }}</label>
                                </div>
                            @endforeach
                            <div class="ms-auto d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-light" wire:click="resetColumns">{{ __('Defaults') }}</button>
                                <button type="button" class="btn btn-sm btn-light" wire:click="$set('columnsOpen', false)">{{ __('Done') }}</button>
                            </div>
                        </div>
                        <div class="form-text">{{ __('Your selection is saved to your account.') }}</div>
                    </div>
                </div>
            @endif
        @else
            <div class="rd-toolbar">
                <div class="alert alert-warning py-2 mb-0 w-100">
                    <i class="ri-delete-bin-line me-1"></i>{{ __('Recycle bin — devices here are hidden from the console and API but not destroyed.') }}
                </div>
            </div>
        @endunless

        {{-- Bulk actions bar (issues #15, #47) — appears when rows are selected,
             on phones too now that the cards carry checkboxes. --}}
        @if (! $trashed && ($selected !== [] || $bulkResult !== ''))
            <div class="rd-toolbar d-flex flex-wrap gap-2 align-items-center">
                @if ($selected !== [])
                    <span class="fs-13 fw-semibold">{{ __(':count selected', ['count' => count($selected)]) }}</span>
                    @if (auth()->user()?->consoleAllows('address_book', 'rw'))
                        <button type="button" class="btn btn-sm btn-light" wire:click="openAbPicker">
                            <i class="ri-contacts-book-2-line me-1"></i>{{ __('Add to Address Book…') }}
                        </button>
                    @endif
                    @if (auth()->user()?->consoleAllows('device', 'rw'))
                        <button type="button" class="btn btn-sm btn-light" wire:click="openGroupPicker">
                            <i class="ri-folder-transfer-line me-1"></i>{{ __('Move to Group…') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-light" wire:click="setIncomingOnly(true)"
                                title="{{ __('The ID server refuses sessions these devices start') }}">
                            <i class="ri-login-box-line me-1"></i>{{ __('Incoming only') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-light" wire:click="setIncomingOnly(false)"
                                title="{{ __('Let these devices start sessions again') }}">
                            <i class="ri-arrow-left-right-line me-1"></i>{{ __('Allow outgoing') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmDeleteSelected">
                            <i class="ri-delete-bin-line me-1"></i>{{ __('Delete') }}
                        </button>
                    @endif
                    <a href="javascript:void(0);" class="fs-13 text-muted" wire:click="clearSelection">{{ __('Clear') }}</a>
                @endif
                @if ($bulkResult !== '')
                    <span class="fs-13 text-success"><i class="ri-check-line me-1"></i>{{ $bulkResult }}</span>
                @endif
            </div>
        @endif

        {{-- Desktop table (md and up) --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover table-centered mb-0">
                <thead>
                <tr>
                    <th class="rd-selcol">
                        @unless ($trashed)
                            @php $pageIds = $devices->pluck('id')->map(fn ($i) => (string) $i); @endphp
                            @if ($pageIds->isNotEmpty() && $pageIds->diff($selected)->isEmpty())
                                <input type="checkbox" class="form-check-input" checked
                                       wire:click="clearSelection" title="{{ __('Clear selection') }}">
                            @else
                                <input type="checkbox" class="form-check-input"
                                       wire:click="selectPage" title="{{ __('Select every row on this page') }}">
                            @endif
                        @endunless
                    </th>
                    <x-sortable-th field="id" :sort="$sortField" :dir="$sortDirection">ID</x-sortable-th>
                    @foreach (['device' => 'Device', 'alias' => 'Alias', 'group' => 'Group', 'owner' => 'Owner', 'version' => 'Version'] as $key => $label)
                        @if ($cols[$key])
                            <x-sortable-th :field="$key" :sort="$sortField" :dir="$sortDirection">{{ __($label) }}</x-sortable-th>
                        @endif
                    @endforeach
                    @if ($cols['os'])<x-sortable-th field="os" :sort="$sortField" :dir="$sortDirection">{{ __('OS') }}</x-sortable-th>@endif
                    @if ($cols['username'])<th>{{ __('User') }}</th>@endif
                    @if ($cols['ip'])<th>IP</th>@endif
                    @if ($cols['lan_ip'])<th title="{{ __('Last seen during a connection') }}">LAN IP</th>@endif
                    @if ($cols['cpu'])<th>CPU</th>@endif
                    @if ($cols['memory'])<th>{{ __('Memory') }}</th>@endif
                    @if ($cols['uuid'])<th>UUID</th>@endif
                    @foreach (['first_seen' => 'First Seen', 'last_seen' => 'Last Seen'] as $key => $label)
                        @if ($cols[$key])
                            <x-sortable-th :field="$key" :sort="$sortField" :dir="$sortDirection">{{ __($label) }}</x-sortable-th>
                        @endif
                    @endforeach
                    <x-sortable-th field="status" :sort="$sortField" :dir="$sortDirection">{{ __('Status') }}</x-sortable-th>
                    <th class="text-end">{{ __('Action') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($devices as $device)
                    <tr wire:key="d{{ $device->id }}">
                        <td class="rd-selcol">
                            @unless ($trashed)
                                <input type="checkbox" class="form-check-input" value="{{ $device->id }}"
                                       wire:model.live="selected">
                            @endunless
                        </td>
                        <td>
                            <x-platform-icon :platform="$device->platform()" class="me-1"/>
                            @if ($trashed)
                                <span class="fw-semibold">{{ $device->rustdesk_id }}</span>
                            @else
                                <a href="riqx://{{ $device->rustdesk_id }}" class="fw-semibold"
                                   title="{{ __('Connect with RustDesk') }}">{{ $device->rustdesk_id }}</a>
                            @endif
                        </td>
                        @if ($cols['device'])
                            <td>
                                @if ($trashed)
                                    <span class="rd-cell-title">{{ $device->hostname ?: '—' }}</span>
                                @else
                                    <a href="{{ route('devices.show', $device->id) }}" class="rd-cell-title rd-cell-link">{{ $device->hostname ?: '—' }}</a>
                                @endif
                                {{-- The subtitle only carries what has no column of its own on
                                     screen; enable OS or User and it leaves here (issue #33). --}}
                                @php
                                    $subParts = array_filter([
                                        $cols['os'] ? null : ($device->os ? \Illuminate\Support\Str::limit($device->osDescription(), 28) : null),
                                        $cols['username'] ? null : ($device->username ?: null),
                                    ]);
                                @endphp
                                @if ($subParts !== [])
                                    <span class="rd-cell-sub">{{ implode(' · ', $subParts) }}</span>
                                @endif
                            </td>
                        @endif
                        @if ($cols['alias'])
                            <td>
                                @if ($device->alias && ! $trashed)
                                    <a href="{{ route('devices.show', $device->id) }}" class="rd-cell-link">{{ $device->alias }}</a>
                                @else
                                    {{ $device->alias ?: '—' }}
                                @endif
                            </td>
                        @endif
                        @if ($cols['group'])<td>{{ $device->group?->name ?: '—' }}</td>@endif
                        @if ($cols['owner'])
                            <td>
                                @if ($device->user)
                                    <span class="badge bg-info-subtle text-info">{{ $device->user->username }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        @endif
                        @if ($cols['version'])
                            <td><span class="badge bg-secondary-subtle text-secondary">{{ $device->version ?: '?' }}</span></td>
                        @endif
                        @if ($cols['os'])<td class="rd-nowrap" title="{{ $device->os }}">{{ $device->os ? \Illuminate\Support\Str::limit($device->osDescription(), 34) : '—' }}</td>@endif
                        @if ($cols['username'])<td>{{ $device->username ?: '—' }}</td>@endif
                        @if ($cols['ip'])<td class="rd-mono fs-13 rd-nowrap">{{ $device->last_online_ip ?: '—' }}</td>@endif
                        @if ($cols['lan_ip'])
                            <td class="rd-mono fs-13 rd-nowrap"
                                title="{{ $device->lan_ip ? __('Seen during a connection :time', ['time' => $device->lan_ip_seen_at?->diffForHumans()]) : __('Not seen yet. Learned when a session is set up on the same network.') }}">{{ $device->lan_ip ?: '—' }}</td>
                        @endif
                        @if ($cols['cpu'])
                            <td><span class="fs-13" title="{{ $device->cpu }}">{{ $device->cpu ? \Illuminate\Support\Str::limit($device->cpu, 24) : '—' }}</span></td>
                        @endif
                        @if ($cols['memory'])<td class="fs-13">{{ $device->memory ?: '—' }}</td>@endif
                        @if ($cols['uuid'])
                            <td><span class="rd-mono fs-13" title="{{ $device->uuid }}">{{ $device->uuid ? \Illuminate\Support\Str::limit($device->uuid, 12) : '—' }}</span></td>
                        @endif
                        @if ($cols['first_seen'])
                            <td><span title="{{ \App\Support\LocalDate::dateTimeSeconds($device->created_at) }}">{{ \App\Support\LocalDate::date($device->created_at) ?? '—' }}</span></td>
                        @endif
                        @if ($cols['last_seen'])
                            <td>
                                <span title="{{ \App\Support\LocalDate::dateTimeSeconds($device->last_online_at) }}">
                                    {{ $device->last_online_at?->diffForHumans() ?? __('never') }}
                                </span>
                            </td>
                        @endif
                        <td>
                            @if ($trashed)
                                <span class="badge bg-warning-subtle text-warning">{{ __('Deleted') }}</span>
                            @elseif ($device->isOnline())
                                <span class="badge bg-success-subtle text-success"><i class="rd-dot"></i>{{ __('Online') }}</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary"><i class="rd-dot"></i>{{ __('Offline') }}</span>
                            @endif
                            @if ($device->isIncomingOnly())
                                <span class="badge bg-info-subtle text-info ms-1" title="{{ __('Can be controlled, cannot start sessions') }}">{{ __('Incoming only') }}</span>
                            @endif
                            @include('livewire.partials.device-duplicate-badge', ['class' => 'ms-1'])
                        </td>
                        <td class="text-end rd-rowact">
                            @if ($trashed)
                                @if (auth()->user()?->consoleAllows('device', 'rw'))
                                    <a href="javascript:void(0);" class="text-success me-2" wire:click="restoreDevice({{ $device->id }})">{{ __('Restore') }}</a>
                                    <a href="javascript:void(0);" class="text-danger"
                                       wire:click="forceDeleteDevice({{ $device->id }})"
                                       wire:confirm="{{ __('PERMANENTLY delete device :id? This cannot be undone.', ['id' => $device->rustdesk_id]) }}">{{ __('Destroy') }}</a>
                                @endif
                            @else
                                {{-- Row actions are neutral; only the destructive one
                                     takes a colour. Two full-colour ordinary actions
                                     side by side read louder than Edit and make this
                                     the only cell in the console with four colours. --}}
                                @if (config('cortendesk.native_webclient'))
                                    <a href="{{ route('webclient') }}?id={{ $device->rustdesk_id }}"
                                       target="cortendesk-webclient" rel="noopener" class="rd-act me-2"
                                       title="{{ __('Connect in the browser (native client)') }}"><i class="ri-remote-control-line me-1"></i>{{ __('Connect') }}</a>
                                @endif
                                @if (config('cortendesk.webclient_url'))
                                    <a href="{{ config('cortendesk.webclient_url') }}?id={{ $device->rustdesk_id }}"
                                       target="cortendesk-webclient" rel="noopener" class="rd-act me-2"
                                       title="{{ __('Connect in the browser') }}">{{ __('Web Client') }}</a>
                                @endif
                                <a href="{{ route('devices.show', $device->id) }}" class="rd-act me-2" title="{{ __('View details') }}"><i class="ri-eye-line"></i></a>
                                @if (auth()->user()?->consoleAllows('device', 'rw'))
                                    <a href="javascript:void(0);" class="rd-act me-2" wire:click="edit({{ $device->id }})">{{ __('Edit') }}</a>
                                    <a href="javascript:void(0);" class="text-danger"
                                       wire:click="confirmDelete({{ $device->id }})">{{ __('Delete') }}</a>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $colspan }}" class="rd-empty-cell">
                            <div class="rd-empty">
                                <div class="rd-empty-icon"><i class="{{ $trashed ? 'ri-delete-bin-line' : 'ri-computer-line' }}"></i></div>
                                <p class="rd-empty-title">{{ $trashed ? __('Recycle bin is empty.') : __('No devices match your filters.') }}</p>
                                <p class="rd-empty-text">
                                    {{ $trashed
                                        ? __('Deleted devices land here until they are destroyed for good.')
                                        : __('Devices register themselves the first time a RustDesk client signs in to this server.') }}
                                </p>
                                @unless ($trashed)
                                    <button type="button" class="btn btn-sm btn-outline-light" wire:click="resetFilters">{{ __('Clear filters') }}</button>
                                    @if (auth()->user()?->consoleAllows('setting', 'r'))
                                        <a href="{{ route('setup') }}" class="btn btn-sm btn-primary">{{ __('Set up a client') }}</a>
                                    @endif
                                @endunless
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile card list (below md) --}}
        <div class="d-md-none rd-cardlist">
            @forelse ($devices as $device)
                <div class="rd-mini" wire:key="m{{ $device->id }}">
                    <div class="rd-mini-head">
                        <div class="d-flex align-items-center gap-2 min-width-0">
                            <x-platform-icon :platform="$device->platform()" size="fs-22"/>
                            <div class="min-width-0">
                                @if ($trashed)
                                    <span class="rd-mini-title text-truncate">{{ $device->rustdesk_id }}</span>
                                @else
                                    <a href="riqx://{{ $device->rustdesk_id }}" class="rd-mini-title text-truncate"
                                       title="{{ __('Connect with RustDesk') }}">{{ $device->rustdesk_id }}</a>
                                @endif
                                @if ($trashed)
                                    <span class="rd-mini-sub text-truncate">{{ $device->alias ?: $device->hostname }}</span>
                                @else
                                    <a href="{{ route('devices.show', $device->id) }}" class="rd-mini-sub text-truncate">{{ $device->alias ?: $device->hostname ?: __('Details') }}</a>
                                @endif
                            </div>
                        </div>
                        @unless ($trashed)
                            <input type="checkbox" class="form-check-input flex-shrink-0" value="{{ $device->id }}"
                                   wire:model.live="selected" aria-label="{{ __('Select device :id', ['id' => $device->rustdesk_id]) }}">
                        @endunless
                        @if ($trashed)
                            <span class="badge bg-warning-subtle text-warning flex-shrink-0">{{ __('Deleted') }}</span>
                        @elseif ($device->isOnline())
                            <span class="badge bg-success-subtle text-success flex-shrink-0">{{ __('Online') }}</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary flex-shrink-0">{{ __('Offline') }}</span>
                        @endif
                    </div>
                    @if ($device->isIncomingOnly() || $duplicates?->has($device->id))
                        <div class="mb-1 d-flex flex-wrap gap-1">
                            @if ($device->isIncomingOnly())
                                <span class="badge bg-info-subtle text-info" title="{{ __('Can be controlled, cannot start sessions') }}">{{ __('Incoming only') }}</span>
                            @endif
                            @include('livewire.partials.device-duplicate-badge')
                        </div>
                    @endif
                    <div class="rd-mini-foot">
                        <span class="rd-mini-sub min-width-0">
                            {{ $device->username }} · v{{ $device->version ?: '?' }} ·
                            <span class="text-nowrap">{{ $device->last_online_at?->diffForHumans(short: true) ?? __('never') }}</span>
                        </span>
                        <div class="rd-mini-acts">
                            @if ($trashed)
                                @if (auth()->user()?->consoleAllows('device', 'rw'))
                                    <a href="javascript:void(0);" class="rd-iconbtn text-success" title="{{ __('Restore') }}" wire:click="restoreDevice({{ $device->id }})"><i class="ri-arrow-go-back-line"></i></a>
                                    <a href="javascript:void(0);" class="rd-iconbtn text-danger" title="{{ __('Destroy') }}"
                                       wire:click="forceDeleteDevice({{ $device->id }})"
                                       wire:confirm="{{ __('PERMANENTLY delete device :id?', ['id' => $device->rustdesk_id]) }}"><i class="ri-close-circle-line"></i></a>
                                @endif
                            @else
                                @if (config('cortendesk.native_webclient'))
                                    <a href="{{ route('webclient') }}?id={{ $device->rustdesk_id }}"
                                       target="cortendesk-webclient" rel="noopener" class="rd-iconbtn"
                                       title="{{ __('Connect in the browser (native client)') }}"><i class="ri-remote-control-line"></i></a>
                                @endif
                                @if (config('cortendesk.webclient_url'))
                                    <a href="{{ config('cortendesk.webclient_url') }}?id={{ $device->rustdesk_id }}"
                                       target="cortendesk-webclient" rel="noopener" class="rd-iconbtn"
                                       title="{{ __('Connect in the browser') }}"><i class="ri-global-line"></i></a>
                                @endif
                                <a href="{{ route('devices.show', $device->id) }}" class="rd-iconbtn" title="{{ __('View details') }}"><i class="ri-eye-line"></i></a>
                                @if (auth()->user()?->consoleAllows('device', 'rw'))
                                    <a href="javascript:void(0);" class="rd-iconbtn" title="{{ __('Edit') }}" wire:click="edit({{ $device->id }})"><i class="ri-pencil-line"></i></a>
                                    <a href="javascript:void(0);" class="rd-iconbtn text-danger" title="{{ __('Delete') }}"
                                       wire:click="confirmDelete({{ $device->id }})"><i class="ri-delete-bin-line"></i></a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="rd-empty">
                    <div class="rd-empty-icon"><i class="{{ $trashed ? 'ri-delete-bin-line' : 'ri-computer-line' }}"></i></div>
                    <p class="rd-empty-title">{{ $trashed ? __('Recycle bin is empty.') : __('No devices match your filters.') }}</p>
                    <p class="rd-empty-text">
                        {{ $trashed
                            ? __('Deleted devices land here until they are destroyed for good.')
                            : __('Devices register themselves the first time a RustDesk client signs in to this server.') }}
                    </p>
                    @unless ($trashed)
                        <button type="button" class="btn btn-sm btn-outline-light" wire:click="resetFilters">{{ __('Clear filters') }}</button>
                        @if (auth()->user()?->consoleAllows('setting', 'r'))
                            <a href="{{ route('setup') }}" class="btn btn-sm btn-primary">{{ __('Set up a client') }}</a>
                        @endif
                    @endunless
                </div>
            @endforelse
        </div>

        <div class="rd-tablefoot">
            <span>{{ __('Showing :from–:to of :total', ['from' => $devices->firstItem() ?? 0, 'to' => $devices->lastItem() ?? 0, 'total' => $devices->total()]) }}</span>
            {{ $devices->links() }}
        </div>
    </div>

    {{-- "Add to Address Book" picker (issue #15) --}}
    @if ($abPickerOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.6);" wire:keydown.escape="closeAbPicker">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form wire:submit="addSelectedToBook">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ trans_choice('Add :count device to an address book|Add :count devices to an address book', count($selected), ['count' => count($selected)]) }}</h5>
                            <button type="button" class="btn-close" wire:click="closeAbPicker"></button>
                        </div>
                        <div class="modal-body">
                            <label class="form-label">{{ __('Address book') }}</label>
                            <select class="form-select @error('abBookId') is-invalid @enderror" wire:model="abBookId">
                                <option value="0">{{ __('Choose…') }}</option>
                                @foreach ($books as $book)
                                    <option value="{{ $book->id }}">
                                        {{ $book->name }}{{ $book->is_personal ? ' '.__('(personal)') : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('abBookId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">
                                {{ __('Only books you can add entries to are listed. Devices already in the chosen book are skipped, and each entry carries the device\'s alias, hostname, platform and user.') }}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" wire:click="closeAbPicker">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('Add') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @include('livewire.partials.device-edit-modal')

    @include('livewire.partials.device-delete-modal')

    {{-- "Move to Group" picker (issue #47) --}}
    @if ($groupPickerOpen)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="move-group-title" style="background: rgba(0,0,0,.6);" wire:keydown.escape="closeGroupPicker">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form wire:submit="moveSelectedToGroup">
                        <div class="modal-header">
                            <h5 id="move-group-title" class="modal-title">{{ trans_choice('Move :count device to a group|Move :count devices to a group', count($selected), ['count' => count($selected)]) }}</h5>
                            <button type="button" class="btn-close" aria-label="{{ __('Close move to group dialog') }}" wire:click="closeGroupPicker"></button>
                        </div>
                        <div class="modal-body">
                            <label class="form-label" for="move-group-id">{{ __('Device group') }}</label>
                            <select id="move-group-id" class="form-select @error('moveGroupId') is-invalid @enderror" wire:model="moveGroupId">
                                <option value="-1">{{ __('Choose…') }}</option>
                                <option value="0">{{ __('No group') }}</option>
                                @foreach ($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                            @error('moveGroupId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">{{ __('Only device groups you can access are listed.') }}</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" wire:click="closeGroupPicker">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('Move') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
