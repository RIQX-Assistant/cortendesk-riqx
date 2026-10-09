<div>
    @php
        $resourceLabels = [
            'device' => __('Devices'),
            'user' => __('Users'),
            'group' => __('Groups'),
            'strategy' => __('Strategies'),
            'address_book' => __('Address books'),
            'audit' => __('Audit logs'),
        ];
        $levelLabels = ['none' => __('None'), 'r' => __('Read'), 'rw' => __('Read/Write')];
    @endphp

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
            <div>
                <h4 class="header-title">{{ __('API Tokens') }}</h4>
                <p class="rd-card-sub mb-0">{!! __('Scoped Bearer tokens for the automation REST API (:path).', ['path' => '<code>/api/v1/…</code>']) !!}
                    {{ __('Grant each token only the resources your scripts need.') }} {!! __('See :doc.', ['doc' => '<code>docs/admin-api.md</code>']) !!}</p>
            </div>
            @if (auth()->user()?->consoleAllows('token', 'rw'))
                <div class="rd-card-actions">
                    <button type="button" class="btn btn-primary" wire:click="create">
                        <i class="ri-add-line"></i>{{ __('New Token') }}
                    </button>
                </div>
            @endif
        </div>

            {{-- One-time plaintext reveal --}}
            @if ($plaintext)
                <div class="rd-toolbar">
                <div class="alert alert-success mb-0 w-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="me-2">
                            <strong><i class="ri-check-line me-1"></i>{{ __('Token created.') }}</strong>
                            <div class="fs-13">{{ __('Copy it now — it is shown only once and cannot be retrieved later.') }}</div>
                        </div>
                        <button type="button" class="btn-close" wire:click="dismissPlaintext" aria-label="{{ __('Dismiss') }}"></button>
                    </div>
                    <div class="input-group input-group-sm mt-2">
                        <input type="text" class="form-control font-monospace" readonly value="{{ $plaintext }}">
                        <button class="btn btn-light" type="button"
                                onclick="rdCopyPrevious(this)">
                            <i class="ri-file-copy-line"></i>
                        </button>
                    </div>
                </div>
                </div>
            @endif

            {{-- Desktop table --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover table-centered mb-0">
                    <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Permissions') }}</th>
                        <th>{{ __('Created by') }}</th>
                        <th>{{ __('Last used') }}</th>
                        <th>{{ __('Expires') }}</th>
                        <th class="text-end">{{ __('Action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($tokens as $token)
                        <tr wire:key="t{{ $token->id }}">
                            <td>
                                <div class="rd-cell rd-tone-amber">
                                    <span class="rd-avatar"><i class="ri-key-2-line"></i></span>
                                    <div class="min-width-0">
                                        <span class="rd-cell-title">{{ $token->name }}</span>
                                        <span class="rd-cell-sub rd-mono">{{ $token->token_prefix }}…</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @foreach ($token->permissions as $res => $lvl)
                                    @if ($lvl !== 'none')
                                        <span class="badge {{ $lvl === 'rw' ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary' }}">
                                            {{ $resourceLabels[$res] ?? $res }}: {{ strtoupper($lvl) }}
                                        </span>
                                    @endif
                                @endforeach
                            </td>
                            <td>{{ $token->user?->username ?? '—' }}</td>
                            <td>
                                @if ($token->last_used_at)
                                    <span title="{{ \App\Support\LocalDate::dateTimeSeconds($token->last_used_at) }}">{{ $token->last_used_at->diffForHumans() }}</span>
                                @else
                                    <span class="text-muted">{{ __('Never') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($token->expires_at)
                                    <span class="{{ $token->isExpired() ? 'text-danger' : '' }}" title="{{ \App\Support\LocalDate::dateTimeSeconds($token->expires_at) }}">
                                        {{ \App\Support\LocalDate::date($token->expires_at) }}
                                    </span>
                                @else
                                    <span class="text-muted">{{ __('Never') }}</span>
                                @endif
                            </td>
                            <td class="text-end rd-rowact">
                                @if (auth()->user()?->consoleAllows('token', 'rw'))
                                    <a href="javascript:void(0);" class="text-danger"
                                       wire:click="revoke({{ $token->id }})"
                                       wire:confirm="{{ __('Revoke token “:name”? Any script using it stops working immediately.', ['name' => $token->name]) }}">{{ __('Revoke') }}</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="rd-empty-cell">
                                <div class="rd-empty">
                                    <div class="rd-empty-icon"><i class="ri-key-2-line"></i></div>
                                    <p class="rd-empty-title">{{ __('No API tokens yet.') }}</p>
                                    <p class="rd-empty-text">{{ __('A token lets a script talk to the console API without a password.') }}</p>
                                    @if (auth()->user()?->consoleAllows('token', 'rw'))
                                        <button type="button" class="btn btn-sm btn-outline-light" wire:click="create">{{ __('New Token') }}</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile card list --}}
            <div class="d-md-none rd-cardlist">
                @forelse ($tokens as $token)
                    <div class="rd-mini" wire:key="mt{{ $token->id }}">
                            <div class="rd-mini-head">
                                <div class="min-width-0">
                                    <span class="rd-mini-title text-truncate">{{ $token->name }}</span>
                                    <span class="rd-mini-sub rd-mono">{{ $token->token_prefix }}…</span>
                                </div>
                                @if (auth()->user()?->consoleAllows('token', 'rw'))
                                    <a href="javascript:void(0);" class="rd-iconbtn text-danger flex-shrink-0" title="{{ __('Revoke') }}"
                                       wire:click="revoke({{ $token->id }})"
                                       wire:confirm="{{ __('Revoke token “:name”? Any script using it stops working immediately.', ['name' => $token->name]) }}">
                                        <i class="ri-delete-bin-line"></i>
                                    </a>
                                @endif
                            </div>
                            <div class="mt-2">
                                @foreach ($token->permissions as $res => $lvl)
                                    @if ($lvl !== 'none')
                                        <span class="badge {{ $lvl === 'rw' ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary' }}">
                                            {{ $resourceLabels[$res] ?? $res }}: {{ strtoupper($lvl) }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                            <span class="rd-mini-sub mt-2">
                                {{ __('Last used: :time', ['time' => $token->last_used_at ? $token->last_used_at->diffForHumans() : __('never')]) }} ·
                                {{ __('Expires: :date', ['date' => $token->expires_at ? \App\Support\LocalDate::date($token->expires_at) : __('never')]) }}
                            </span>
                    </div>
                @empty
                    <div class="rd-empty">
                        <div class="rd-empty-icon"><i class="ri-key-2-line"></i></div>
                        <p class="rd-empty-title">{{ __('No API tokens yet.') }}</p>
                        @if (auth()->user()?->consoleAllows('token', 'rw'))
                            <button type="button" class="btn btn-sm btn-outline-light" wire:click="create">{{ __('New Token') }}</button>
                        @endif
                    </div>
                @endforelse
            </div>
    </div>

    {{-- Create modal --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('New API Token') }}</h5>
                            <button type="button" class="btn-close" wire:click="closeModal" aria-label="{{ __('Close') }}"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label" for="at-name">{{ __('Name') }} <span class="text-danger">*</span></label>
                                <input type="text" id="at-name" class="form-control @error('name') is-invalid @enderror"
                                       wire:model="name" placeholder="{{ __('e.g. Ansible provisioning') }}" autocomplete="off">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="at-expires">{{ __('Expires in (days)') }}</label>
                                <input type="number" id="at-expires" min="1" max="3650" style="max-width:160px;"
                                       class="form-control @error('expiresDays') is-invalid @enderror"
                                       wire:model="expiresDays" placeholder="{{ __('Never') }}">
                                @error('expiresDays') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">{{ __('Leave blank for a token that never expires.') }}</div>
                            </div>

                            <label class="form-label">{{ __('Permissions') }}</label>
                            @unless (auth()->user()?->is_admin)
                                <div class="form-text mb-1">{{ __('A token can never be granted more than you hold yourself — anything above your own level is reduced when the token is created.') }}</div>
                            @endunless
                            @error('permissions') <div class="text-danger fs-13 mb-1">{{ $message }}</div> @enderror
                            {{-- Desktop: radio grid --}}
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-sm table-centered mb-0">
                                    <thead>
                                    <tr>
                                        <th>{{ __('Resource') }}</th>
                                        <th class="text-center">{{ __('None') }}</th>
                                        <th class="text-center">{{ __('Read') }}</th>
                                        <th class="text-center">{{ __('Read/Write') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($resources as $res)
                                        <tr wire:key="perm-{{ $res }}">
                                            <td>{{ $resourceLabels[$res] ?? $res }}</td>
                                            @foreach ($levels as $lvl)
                                                <td class="text-center">
                                                    <input class="form-check-input" type="radio"
                                                           aria-label="{{ ($resourceLabels[$res] ?? $res).' '.($levelLabels[$lvl] ?? $lvl) }}"
                                                           wire:model="permissions.{{ $res }}" value="{{ $lvl }}">
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Mobile: one select per resource (four radio columns do not fit 390px) --}}
                            <div class="d-md-none">
                                @foreach ($resources as $res)
                                    <div class="mb-2" wire:key="mperm-{{ $res }}">
                                        <label class="form-label mb-1" for="at-perm-{{ $res }}">{{ $resourceLabels[$res] ?? $res }}</label>
                                        <select id="at-perm-{{ $res }}" class="form-select" wire:model="permissions.{{ $res }}">
                                            @foreach ($levels as $lvl)
                                                <option value="{{ $lvl }}">{{ $levelLabels[$lvl] ?? $lvl }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" wire:click="closeModal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">
                                <span wire:loading.remove wire:target="save"><i class="ri-key-2-line me-1"></i>{{ __('Create Token') }}</span>
                                <span wire:loading wire:target="save">{{ __('Creating…') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
