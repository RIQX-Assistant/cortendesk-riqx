<div>
    @php
        // Roles gate the screen; is_admin additionally gates the two controls
        // that could hand out MORE authority than the actor holds (D4).
        $canManageUsers = auth()->user()?->consoleAllows('user', 'rw');
        $isSuperAdmin = auth()->user()?->is_admin;
    @endphp

    <div class="card">

            {{-- Toolbar --}}
            <div class="rd-toolbar">
                <div class="rd-toolbar-search">
                    <div class="input-group">
                        <span class="input-group-text"><i class="ri-search-line"></i></span>
                        <input type="search" class="form-control" placeholder="{{ __('Search username, name, email…') }}"
                               wire:model.live.debounce.300ms="search">
                    </div>
                </div>
                <select class="form-select rd-toolbar-filter" wire:model.live="role" aria-label="{{ __('Role') }}">
                    <option value="all">{{ __('All roles') }}</option>
                    <option value="admin">{{ __('Administrators') }}</option>
                    <option value="user">{{ __('Users') }}</option>
                </select>
                <select class="form-select rd-toolbar-filter" wire:model.live="status" aria-label="{{ __('Status') }}">
                    <option value="all">{{ __('All statuses') }}</option>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="disabled">{{ __('Disabled') }}</option>
                </select>
                <div class="rd-toolbar-actions">
                    <button type="button" class="btn btn-outline-light" wire:click="resetFilters">{{ __('Reset') }}</button>
                    @if ($canManageUsers)
                        <button type="button" class="btn btn-primary" wire:click="create">
                            <i class="ri-add-line"></i>{{ __('Add User') }}
                        </button>
                    @endif
                </div>
            </div>

            {{-- Desktop table (md and up) --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover table-centered mb-0">
                    <thead>
                    <tr>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Group') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Devices') }}</th>
                        <th>{{ __('Created') }}</th>
                        <th class="text-end">{{ __('Action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($users as $user)
                        @php
                            $canTouch = $canManageUsers && ($isSuperAdmin
                                || (! $user->is_admin && $user->role_id === null && $user->id !== auth()->id()));
                        @endphp
                        <tr wire:key="u{{ $user->id }}">
                            <td>
                                <div class="rd-cell {{ $user->is_admin ? 'rd-tone-accent' : 'rd-tone-purple' }}">
                                    <span class="rd-avatar">{{ strtoupper(substr($user->username, 0, 1)) }}</span>
                                    <div class="min-width-0">
                                        <a href="{{ route('users.show', $user->id) }}" class="rd-cell-title rd-cell-link">{{ $user->username }}</a>
                                        <span class="rd-cell-sub">{{ $user->name ?: '—' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email ?: '—' }}</td>
                            <td>
                                @forelse ($user->groups as $g)
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $g->name }}</span>
                                @empty
                                    —
                                @endforelse
                            </td>
                            <td>
                                @if ($user->is_admin)
                                    <span class="badge bg-danger-subtle text-danger">{{ __('Administrator') }}</span>
                                @elseif ($user->role)
                                    <span class="badge bg-info-subtle text-info">{{ $user->role->name }}</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">{{ __('User') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($user->is_active)
                                    <span class="badge bg-success-subtle text-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">{{ __('Disabled') }}</span>
                                @endif
                                @if ($user->totp_enabled)
                                    <span class="badge bg-info-subtle text-info" title="{{ __('Two-factor authentication enabled') }}"><i class="ri-shield-keyhole-line"></i> 2FA</span>
                                @endif
                            </td>
                            <td>{{ $user->devices_count }}</td>
                            <td>
                                <span class="text-nowrap" title="{{ \App\Support\LocalDate::dateTimeSeconds($user->created_at) }}">{{ \App\Support\LocalDate::date($user->created_at) }}</span>
                            </td>
                            <td class="text-end rd-rowact">
                                <a href="{{ route('users.show', $user->id) }}" class="rd-act me-2" title="{{ __('View details') }}"><i class="ri-eye-line"></i></a>
                                @if ($canTouch)
                                <a href="javascript:void(0);" class="rd-act me-2" wire:click="edit({{ $user->id }})">{{ __('Edit') }}</a>
                                <a href="javascript:void(0);" class="rd-act me-2" wire:click="openAssign({{ $user->id }})">{{ __('Devices') }}</a>
                                @if ($user->id === auth()->id())
                                    <span class="text-muted me-2" title="{{ __('You cannot disable your own account') }}" style="cursor:not-allowed;">
                                        {{ $user->is_active ? __('Disable') : __('Enable') }}
                                    </span>
                                    <span class="text-muted" title="{{ __('You cannot delete your own account') }}" style="cursor:not-allowed;">{{ __('Delete') }}</span>
                                @else
                                    <a href="javascript:void(0);" class="rd-act me-2" wire:click="toggleActive({{ $user->id }})">
                                        {{ $user->is_active ? __('Disable') : __('Enable') }}
                                    </a>
                                    <a href="javascript:void(0);" class="rd-act me-2"
                                       wire:click="forceLogout({{ $user->id }})"
                                       wire:confirm="{{ __('Force :user to log out everywhere? This signs out their RustDesk clients and console sessions.', ['user' => $user->username]) }}">{{ __('Log out') }}</a>
                                    @if ($user->totp_enabled)
                                        <a href="javascript:void(0);" class="text-warning me-2"
                                           wire:click="resetTwoFactor({{ $user->id }})"
                                           wire:confirm="{{ __('Reset 2FA for :user? Their authenticator and recovery codes are removed and they must re-enroll.', ['user' => $user->username]) }}">{{ __('Reset 2FA') }}</a>
                                    @endif
                                    <a href="javascript:void(0);" class="text-danger"
                                       wire:click="deleteUser({{ $user->id }})"
                                       wire:confirm="{{ __('Delete user :user? Their devices will be kept but no longer assigned to any user.', ['user' => $user->username]) }}">{{ __('Delete') }}</a>
                                @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="rd-empty-cell">
                                <div class="rd-empty">
                                    <div class="rd-empty-icon"><i class="ri-user-line"></i></div>
                                    <p class="rd-empty-title">{{ __('No users match your filters.') }}</p>
                                    <p class="rd-empty-text">{{ __('Console accounts sign in with a username, not an email address.') }}</p>
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
                @forelse ($users as $user)
                    @php
                        $canTouch = $canManageUsers && ($isSuperAdmin
                            || (! $user->is_admin && $user->role_id === null && $user->id !== auth()->id()));
                    @endphp
                    <div class="rd-mini" wire:key="mu{{ $user->id }}">
                            <div class="rd-mini-head">
                                <div class="rd-cell {{ $user->is_admin ? 'rd-tone-accent' : 'rd-tone-purple' }}">
                                    <span class="rd-avatar">{{ strtoupper(substr($user->username, 0, 1)) }}</span>
                                    <div class="min-width-0">
                                        <a href="{{ route('users.show', $user->id) }}" class="rd-mini-title text-truncate">{{ $user->username }}</a>
                                        <span class="rd-mini-sub text-truncate">{{ $user->name ?: ($user->email ?: '—') }}</span>
                                    </div>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    @if ($user->is_admin)
                                        <span class="badge bg-danger-subtle text-danger">{{ __('Admin') }}</span>
                                    @elseif ($user->role)
                                        <span class="badge bg-info-subtle text-info">{{ $user->role->name }}</span>
                                    @endif
                                    @if ($user->is_active)
                                        <span class="badge bg-success-subtle text-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">{{ __('Disabled') }}</span>
                                    @endif
                                    @if ($user->totp_enabled)
                                        <span class="badge bg-info-subtle text-info" title="{{ __('2FA enabled') }}"><i class="ri-shield-keyhole-line"></i></span>
                                    @endif
                                </div>
                            </div>
                            {{-- Meta on its own full-width line; actions in one non-wrapping row below --}}
                            <span class="rd-mini-sub mt-2">
                                {{ $user->groups->isNotEmpty() ? $user->groups->pluck('name')->join(', ') : __('No group') }} ·
                                {{ trans_choice(':count device|:count devices', $user->devices_count, ['count' => $user->devices_count]) }} ·
                                <span class="text-nowrap">{{ \App\Support\LocalDate::date($user->created_at) }}</span>
                            </span>
                            <div class="rd-mini-acts justify-content-end mt-2">
                                <a href="{{ route('users.show', $user->id) }}" class="rd-iconbtn" title="{{ __('View details') }}"><i class="ri-eye-line"></i></a>
                                @if ($canTouch)
                                <a href="javascript:void(0);" class="rd-iconbtn" title="{{ __('Edit') }}" wire:click="edit({{ $user->id }})"><i class="ri-pencil-line"></i></a>
                                <a href="javascript:void(0);" class="rd-iconbtn" title="{{ __('Assign devices') }}" wire:click="openAssign({{ $user->id }})"><i class="ri-computer-line"></i></a>
                                @unless ($user->id === auth()->id())
                                    <a href="javascript:void(0);" class="rd-iconbtn" title="{{ $user->is_active ? __('Disable') : __('Enable') }}"
                                       wire:click="toggleActive({{ $user->id }})">
                                        <i class="{{ $user->is_active ? 'ri-user-unfollow-line' : 'ri-user-follow-line' }}"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="rd-iconbtn" title="{{ __('Force logout everywhere') }}"
                                       wire:click="forceLogout({{ $user->id }})"
                                       wire:confirm="{{ __('Force :user to log out everywhere? This signs out their RustDesk clients and console sessions.', ['user' => $user->username]) }}">
                                        <i class="ri-logout-box-r-line"></i>
                                    </a>
                                    @if ($user->totp_enabled)
                                        <a href="javascript:void(0);" class="rd-iconbtn text-warning" title="{{ __('Reset 2FA') }}"
                                           wire:click="resetTwoFactor({{ $user->id }})"
                                           wire:confirm="{{ __('Reset 2FA for :user? Their authenticator and recovery codes are removed and they must re-enroll.', ['user' => $user->username]) }}">
                                            <i class="ri-shield-keyhole-line"></i>
                                        </a>
                                    @endif
                                    <a href="javascript:void(0);" class="rd-iconbtn text-danger" title="{{ __('Delete') }}"
                                       wire:click="deleteUser({{ $user->id }})"
                                       wire:confirm="{{ __('Delete user :user? Their devices will be kept but no longer assigned to any user.', ['user' => $user->username]) }}"><i class="ri-delete-bin-line"></i></a>
                                @endunless
                                @endif
                            </div>
                    </div>
                @empty
                    <div class="rd-empty">
                        <div class="rd-empty-icon"><i class="ri-user-line"></i></div>
                        <p class="rd-empty-title">{{ __('No users match your filters.') }}</p>
                        <p class="rd-empty-text">{{ __('Console accounts sign in with a username, not an email address.') }}</p>
                        <button type="button" class="btn btn-sm btn-outline-light" wire:click="resetFilters">{{ __('Clear filters') }}</button>
                    </div>
                @endforelse
            </div>

            <div class="rd-tablefoot">
                <span>{{ __('Showing :from–:to of :total', ['from' => $users->firstItem() ?? 0, 'to' => $users->lastItem() ?? 0, 'total' => $users->total()]) }}</span>
                {{ $users->links() }}
            </div>
    </div>

    {{-- Create / Edit modal (plain Bootstrap markup, toggled by Livewire) --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $editing ? __('Edit User') : __('Add User') }}</h5>
                            <button type="button" class="btn-close" wire:click="closeModal" aria-label="{{ __('Close') }}"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="ul-username">{{ __('Username') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="ul-username" class="form-control @error('username') is-invalid @enderror"
                                               wire:model="username" autocomplete="off">
                                        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="ul-name">{{ __('Display Name') }}</label>
                                        <input type="text" id="ul-name" class="form-control @error('name') is-invalid @enderror"
                                               wire:model="name" autocomplete="off">
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="ul-email">{{ __('Email') }}</label>
                                        <input type="email" id="ul-email" class="form-control @error('email') is-invalid @enderror"
                                               wire:model="email" autocomplete="off">
                                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="ul-password">
                                            {{ __('Password') }}
                                            @if ($editing)
                                                <small class="text-muted fw-normal">{{ __('(leave blank to keep current)') }}</small>
                                            @else
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <input type="password" id="ul-password" class="form-control @error('password') is-invalid @enderror"
                                               wire:model="password" autocomplete="new-password">
                                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    @if ($isSuperAdmin)
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" role="switch" id="ul-admin" wire:model.live="is_admin">
                                            <label class="form-check-label" for="ul-admin">{{ __('Administrator') }}</label>
                                            <small class="text-muted d-block">{{ __('Admins see and manage everything.') }}</small>
                                        </div>

                                        {{-- Delegated role (PLAN D4). Only a full administrator may
                                             grant one; save() strips it from anyone else's payload. --}}
                                        @unless ($is_admin)
                                            <div class="mb-3">
                                                <label class="form-label" for="ul-role">{{ __('Role') }}</label>
                                                <select id="ul-role" class="form-select @error('role_id') is-invalid @enderror" wire:model="role_id">
                                                    <option value="">{{ __('Standard user') }}</option>
                                                    @foreach ($roles as $r)
                                                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                <small class="text-muted d-block">
                                                    {{ __('A role opens extra console sections. It never widens which devices or address books this user can see — that stays with the grants below.') }}
                                                    <a href="{{ route('roles') }}">{{ __('Manage roles') }}</a>
                                                </small>
                                            </div>
                                        @endunless
                                    @endif
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="ul-active" wire:model="is_active"
                                               @if ($editing === auth()->id()) disabled @endif>
                                        <label class="form-check-label" for="ul-active">{{ __('Active') }}</label>
                                        @if ($editing === auth()->id())
                                            <small class="text-muted d-block">{{ __('You cannot disable your own account.') }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Groups') }}</label>
                                        <p class="text-muted fs-13 mb-2">{{ __('A user can belong to several groups. Groups control address-book sharing and device-group access.') }}</p>
                                        <div class="rd-scrollbox p-2" style="max-height: 210px; overflow-y: auto;">
                                            @forelse ($userGroups as $g)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="ul-ug-{{ $g->id }}"
                                                           value="{{ $g->id }}" wire:model="user_group_ids">
                                                    <label class="form-check-label" for="ul-ug-{{ $g->id }}">{{ $g->name }}</label>
                                                </div>
                                            @empty
                                                <p class="text-muted fs-13 mb-0">{{ auth()->user()?->is_admin ? __('No user groups exist yet.') : __('No user groups you can grant.') }}</p>
                                            @endforelse
                                        </div>
                                        @error('user_group_ids') <div class="text-danger fs-13">{{ $message }}</div> @enderror
                                        @error('user_group_ids.*') <div class="text-danger fs-13">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- Device-group access (non-admins only) --}}
                                    @unless ($is_admin)
                                        <div class="mb-1">
                                            <label class="form-label">{{ __('Device access') }}</label>
                                            <p class="text-muted fs-13 mb-2">{{ __('This user sees only devices they own plus devices in the groups checked below.') }}</p>
                                            <div class="rd-scrollbox p-2" style="max-height: 210px; overflow-y: auto;">
                                                @forelse ($deviceGroups as $dg)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="ul-dg-{{ $dg->id }}"
                                                               value="{{ $dg->id }}" wire:model="device_group_ids">
                                                        <label class="form-check-label" for="ul-dg-{{ $dg->id }}">{{ $dg->name }}</label>
                                                    </div>
                                                @empty
                                                    <p class="text-muted fs-13 mb-0">{{ auth()->user()?->is_admin ? __('No device groups exist yet.') : __('No device groups you can grant.') }}</p>
                                                @endforelse
                                            </div>
                                        </div>
                                    @endunless
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" wire:click="closeModal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">
                                <span wire:loading.remove wire:target="save">{{ $editing ? __('Save Changes') : __('Create User') }}</span>
                                <span wire:loading wire:target="save">{{ __('Saving…') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    {{-- Assign devices modal: bulk-set which devices this user owns --}}
    @if ($showAssignModal)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true" wire:key="assign-modal">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Assign Devices') }}</h5>
                        <button type="button" class="btn-close" wire:click="closeAssign" aria-label="{{ __('Close') }}"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted fs-13">
                            {!! __('Select the devices owned by :user.', ['user' => '<strong>'.e(optional(\App\Models\User::find($assignUserId))->username).'</strong>']) !!}
                            {{ __('Checked devices become theirs; unchecking a device they currently own releases it (no owner).') }}
                        </p>
                        <input type="search" class="form-control mb-2" placeholder="{{ __('Search ID, alias, hostname…') }}"
                               wire:model.live.debounce.300ms="assignSearch">
                        <div class="rd-scrollbox" style="max-height: 340px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0">
                                <tbody>
                                    @forelse ($assignDevices as $d)
                                        <tr wire:key="ad{{ $d->id }}">
                                            <td style="width:38px;">
                                                <input class="form-check-input" type="checkbox" id="ul-ad-{{ $d->id }}"
                                                       value="{{ $d->id }}" wire:model="assignDeviceIds"
                                                       aria-label="{{ __('Assign device :id', ['id' => $d->rustdesk_id]) }}">
                                            </td>
                                            <td>
                                                {{-- The label is the tap target: a 14px checkbox is not one, and
                                                     `for` makes the whole cell toggle it without a second binding. --}}
                                                <label class="rd-picklabel" for="ul-ad-{{ $d->id }}">
                                                    <span class="fw-semibold">{{ $d->rustdesk_id }}</span>
                                                    @if ($d->alias || $d->hostname)
                                                        <small class="text-muted d-block">{{ $d->alias ?: $d->hostname }}</small>
                                                    @endif
                                                </label>
                                            </td>
                                            <td class="text-end">
                                                @if ($d->user_id && $d->user_id !== $assignUserId)
                                                    <span class="badge bg-secondary-subtle text-secondary">{{ __('owned by :user', ['user' => optional($d->user)->username ?? '—']) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td class="text-center text-muted py-3">{{ __('No devices match.') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted">{{ __(':count selected', ['count' => count($assignDeviceIds)]) }} @if($assignDevices->count() >= 200) · {{ __('showing first 200, refine with search') }} @endif</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" wire:click="closeAssign">{{ __('Cancel') }}</button>
                        <button type="button" class="btn btn-primary" wire:click="saveAssign">
                            <span wire:loading.remove wire:target="saveAssign">{{ __('Save Assignment') }}</span>
                            <span wire:loading wire:target="saveAssign">{{ __('Saving…') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
