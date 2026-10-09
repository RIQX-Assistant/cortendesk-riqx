<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
            <div>
                <h4 class="header-title">{{ __('Invitations') }}</h4>
                <p class="rd-card-sub mb-0">
                    {{ __('Invite someone by email: they pick their own password and land straight in the console with exactly the role and groups you choose here.') }}
                    @unless ($mailEnabled)
                        <span class="text-warning">{{ __('Email is not configured, so nothing is sent — copy the link below and pass it on yourself') }}
                        (<a href="{{ route('settings') }}?tab=email">{{ __('Settings → Email') }}</a>).</span>
                    @endunless
                </p>
            </div>
            <div class="rd-card-actions">
                <button type="button" class="btn btn-primary" wire:click="create">
                    <i class="ri-user-add-line"></i>{{ __('Invite User') }}
                </button>
            </div>
        </div>

            {{-- The plaintext token is unrecoverable, so the link is shown once. --}}
            @if ($inviteUrl)
                <div class="rd-toolbar">
                <div class="alert {{ $mailSent ? 'alert-success' : 'alert-warning' }} mb-0 w-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="me-2">
                            <strong>
                                <i class="{{ $mailSent ? 'ri-check-line' : 'ri-error-warning-line' }} me-1"></i>
                                {{ $mailSent ? __('Invitation emailed to :email.', ['email' => $inviteFor]) : __('Invitation created — but no email was sent.') }}
                            </strong>
                            <div class="fs-13">{{ __('Copy the link now: it is shown only once and cannot be retrieved later.') }}</div>
                        </div>
                        <button type="button" class="btn-close" wire:click="dismissLink" aria-label="{{ __('Dismiss') }}"></button>
                    </div>
                    <div class="input-group input-group-sm mt-2">
                        <input type="text" class="form-control font-monospace" readonly value="{{ $inviteUrl }}">
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
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Username') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Invited by') }}</th>
                        <th>{{ __('Expires') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($invitations as $invite)
                        <tr wire:key="inv{{ $invite->id }}">
                            <td class="text-truncate" style="max-width:220px;">{{ $invite->email }}</td>
                            <td class="font-monospace">{{ $invite->username }}</td>
                            <td>
                                @if ($invite->is_admin)
                                    <span class="badge bg-danger-subtle text-danger">{{ __('Administrator') }}</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">{{ __('User') }}</span>
                                @endif
                            </td>
                            <td>{{ $invite->inviter?->username ?? '—' }}</td>
                            <td>
                                @if ($invite->isExpired())
                                    <span class="badge bg-danger-subtle text-danger">{{ __('Expired') }}</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning"
                                          title="{{ \App\Support\LocalDate::dateTimeSeconds($invite->expires_at) }}">{{ $invite->expires_at->diffForHumans() }}</span>
                                @endif
                            </td>
                            <td class="text-end rd-rowact">
                                @if (in_array($invite->id, $manageableIds, true))
                                    <a href="javascript:void(0);" class="rd-act me-2" wire:click="resend({{ $invite->id }})"
                                       wire:confirm="{{ __('Re-send this invitation? The previous link stops working.') }}">{{ __('Resend') }}</a>
                                    <a href="javascript:void(0);" class="text-danger" wire:click="revoke({{ $invite->id }})"
                                       wire:confirm="{{ __('Revoke the invitation for :email?', ['email' => $invite->email]) }}">{{ __('Revoke') }}</a>
                                @else
                                    <span class="text-muted fs-13" title="{{ __('Only a full administrator can resend or revoke this one.') }}">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="rd-empty-cell">
                                <div class="rd-empty">
                                    <div class="rd-empty-icon"><i class="ri-mail-send-line"></i></div>
                                    <p class="rd-empty-title">{{ __('No pending invitations.') }}</p>
                                    <p class="rd-empty-text">{{ __('An invitation lets someone set their own password without you ever handling it.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile card list --}}
            <div class="d-md-none rd-cardlist">
                @forelse ($invitations as $invite)
                    <div class="rd-mini" wire:key="minv{{ $invite->id }}">
                            <div class="rd-mini-head">
                                <div class="min-width-0">
                                    <span class="rd-mini-title text-truncate">{{ $invite->email }}</span>
                                    <span class="rd-mini-sub rd-mono">{{ $invite->username }}</span>
                                </div>
                                @if (in_array($invite->id, $manageableIds, true))
                                    <a href="javascript:void(0);" class="rd-iconbtn text-danger flex-shrink-0" title="{{ __('Revoke') }}"
                                       wire:click="revoke({{ $invite->id }})"
                                       wire:confirm="{{ __('Revoke the invitation for :email?', ['email' => $invite->email]) }}">
                                        <i class="ri-delete-bin-line"></i>
                                    </a>
                                @endif
                            </div>
                            <div class="mt-2">
                                @if ($invite->is_admin)
                                    <span class="badge bg-danger-subtle text-danger">{{ __('Administrator') }}</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">{{ __('User') }}</span>
                                @endif
                                @if ($invite->isExpired())
                                    <span class="badge bg-danger-subtle text-danger">{{ __('Expired') }}</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">{{ $invite->expires_at->diffForHumans() }}</span>
                                @endif
                            </div>
                            <div class="rd-mini-foot">
                                <span class="rd-mini-sub">{{ __('Invited by :user', ['user' => $invite->inviter?->username ?? '—']) }}</span>
                                {{-- A bare text link is an 18px-tall target. Resend is the one action
                                     this card offers besides Revoke, so it gets button chrome and,
                                     with it, the 40px minimum height phones are held to. --}}
                                @if (in_array($invite->id, $manageableIds, true))
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-light flex-shrink-0"
                                       wire:click="resend({{ $invite->id }})"
                                       wire:confirm="{{ __('Re-send this invitation? The previous link stops working.') }}">{{ __('Resend') }}</a>
                                @endif
                            </div>
                    </div>
                @empty
                    <div class="rd-empty">
                        <div class="rd-empty-icon"><i class="ri-mail-send-line"></i></div>
                        <p class="rd-empty-title">{{ __('No pending invitations.') }}</p>
                    </div>
                @endforelse
            </div>
    </div>

    {{-- Invite modal --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('Invite User') }}</h5>
                            <button type="button" class="btn-close" wire:click="closeModal" aria-label="{{ __('Close') }}"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label" for="inv-email">{{ __('Email') }} <span class="text-danger">*</span></label>
                                <input type="email" id="inv-email" class="form-control @error('email') is-invalid @enderror"
                                       wire:model="email" autocomplete="off" placeholder="person@example.com">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="inv-username">{{ __('Username') }} <span class="text-danger">*</span></label>
                                <input type="text" id="inv-username" class="form-control @error('username') is-invalid @enderror"
                                       wire:model="username" autocomplete="off" placeholder="{{ __('e.g. jsmith') }}">
                                @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">{{ __('Console sign-in is by username, so you pick it — not the invitee.') }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="inv-name">{{ __('Display name') }}</label>
                                <input type="text" id="inv-name" class="form-control @error('name') is-invalid @enderror"
                                       wire:model="name" autocomplete="off" placeholder="{{ __('Optional') }}">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            @if (auth()->user()?->is_admin)
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="inv-admin" wire:model.live="is_admin">
                                        <label class="form-check-label" for="inv-admin">{{ __('Administrator') }}</label>
                                    </div>
                                    <div class="form-text">{{ __('Administrators see every device and every console setting.') }}</div>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label" for="inv-user-groups">{{ __('User groups') }}</label>
                                <select id="inv-user-groups" class="form-select" multiple size="4" wire:model="user_group_ids">
                                    @foreach ($userGroups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @unless ($is_admin)
                                <div class="mb-3">
                                    <label class="form-label" for="inv-device-groups">{{ __('Device groups they may access') }}</label>
                                    <select id="inv-device-groups" class="form-select" multiple size="4" wire:model="device_group_ids">
                                        @foreach ($deviceGroups as $group)
                                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endunless

                            <p class="text-muted fs-13 mb-0">
                                {{ __('The link is single-use and expires in :hours hours.', ['hours' => \App\Models\Invitation::expiryHours()]) }}
                                {{ __('Nothing about the role travels in the URL — it is stored here.') }}
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" wire:click="closeModal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">
                                <span wire:loading.remove wire:target="save"><i class="ri-mail-send-line me-1"></i>{{ __('Send Invitation') }}</span>
                                <span wire:loading wire:target="save">{{ __('Sending…') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
