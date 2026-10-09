<div>
    {{-- ---------------------------------------------------------------- Profile --}}
    <div class="card">
        <div class="card-header">
            <h4 class="header-title">{{ __('Profile') }}</h4>
        </div>
        <div class="card-body">
            {{-- Identity strip: who you are signed in as, before the fields that change it. --}}
            <div class="rd-cell {{ $user->is_admin ? 'rd-tone-accent' : 'rd-tone-purple' }} mb-3">
                <span class="rd-avatar rd-avatar-lg">{{ strtoupper(substr($user->username, 0, 1)) }}</span>
                <div class="min-width-0">
                    <span class="rd-cell-title">{{ $user->name ?: $user->username }}</span>
                    <span class="rd-cell-sub">
                        {{ $user->email ?: __('no email on file') }}
                        @if ($user->isSsoLinked())
                            · {{ __('signed in with SSO') }}
                        @endif
                    </span>
                </div>
            </div>

            @if ($profileSaved)
                <div class="alert alert-success py-2" wire:poll.4s="$set('profileSaved', false)">
                    <i class="ri-check-line me-1"></i>{{ __('Profile saved.') }}
                </div>
            @endif

            <form wire:submit="saveProfile">
                <div class="mb-3">
                    <label class="form-label">{{ __('Username') }}</label>
                    <input type="text" class="form-control" value="{{ $user->username }}" disabled>
                    <div class="form-text">
                        {{ __('Your sign-in name. Only an administrator can change it.') }}
                    </div>
                </div>

                <div class="mb-3">
                    <label for="account-name" class="form-label">{{ __('Display name') }}</label>
                    <input type="text" id="account-name" class="form-control @error('name') is-invalid @enderror"
                           wire:model="name" placeholder="{{ __('Your name') }}">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="account-email" class="form-label">{{ __('Email') }}</label>
                    <input type="email" id="account-email" class="form-control @error('email') is-invalid @enderror"
                           wire:model="email" placeholder="you@example.com">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @if ($user->isSsoLinked())
                        <div class="form-text">
                            <i class="ri-information-line me-1"></i>{{ __('Your name and email come from your identity provider and may be overwritten the next time you sign in with SSO.') }}
                        </div>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i>{{ __('Save Changes') }}
                </button>
            </form>
        </div>
    </div>

    {{-- --------------------------------------------------------------- Password --}}
    @if ($user->isSsoProvisioned())
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">{{ __('Password') }}</h4>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-start gap-2 rd-inset mb-0">
                    <i class="ri-shield-user-line fs-20 text-primary"></i>
                    <div class="text-muted">
                        {{ __('This account signs in through single sign-on, so it has no console password to change.') }}
                        {{ __('Manage your credentials with your identity provider.') }}
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">{{ __('Change Password') }}</h4>
            </div>
            <div class="card-body">
                @if ($passwordSaved)
                    <div class="alert alert-success py-2" wire:poll.4s="$set('passwordSaved', false)">
                        <i class="ri-check-line me-1"></i>{{ __('Password changed.') }}
                    </div>
                @endif

                <form wire:submit="updatePassword">
                    <div class="mb-3">
                        <label for="current-password" class="form-label">{{ __('Current password') }}</label>
                        <input type="password" id="current-password" autocomplete="current-password"
                               class="form-control @error('currentPassword') is-invalid @enderror"
                               wire:model="currentPassword">
                        @error('currentPassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new-password" class="form-label">{{ __('New password') }}</label>
                        <input type="password" id="new-password" autocomplete="new-password"
                               class="form-control @error('password') is-invalid @enderror"
                               wire:model="password">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">{{ __('At least 8 characters.') }}</div>
                    </div>

                    <div class="mb-3">
                        <label for="confirm-password" class="form-label">{{ __('Confirm new password') }}</label>
                        <input type="password" id="confirm-password" autocomplete="new-password"
                               class="form-control @error('passwordConfirmation') is-invalid @enderror"
                               wire:model="passwordConfirmation">
                        @error('passwordConfirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-outline-primary">
                        <i class="ri-key-2-line me-1"></i>{{ __('Change Password') }}
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- ------------------------------------------------------------- Appearance --}}
    <div class="card">
        <div class="card-header">
            <h4 class="header-title">{{ __('Appearance') }}</h4>
        </div>
        <div class="card-body">
            <div class="btn-group" role="group" aria-label="{{ __('Theme') }}">
                <input type="radio" class="btn-check" id="theme-dark" value="dark" wire:model.live="theme">
                <label class="btn btn-outline-primary" for="theme-dark"><i class="ri-moon-line me-1"></i>{{ __('Dark') }}</label>
                <input type="radio" class="btn-check" id="theme-light" value="light" wire:model.live="theme">
                <label class="btn btn-outline-primary" for="theme-light"><i class="ri-sun-line me-1"></i>{{ __('Light') }}</label>
                <input type="radio" class="btn-check" id="theme-system" value="system" wire:model.live="theme">
                <label class="btn btn-outline-primary" for="theme-system"><i class="ri-computer-line me-1"></i>{{ __('Match system') }}</label>
            </div>
            @error('theme') <div class="text-danger fs-13 mt-2">{{ $message }}</div> @enderror
            <p class="text-muted fs-13 mb-0 mt-2">{{ __('Saved to your account, so it applies in every browser you sign in from. The switch in the top bar changes it too.') }}</p>

            {{-- RIQX: console language, saved on the account like the theme. --}}
            <h5 class="mt-4 mb-2">{{ __('Display language') }}</h5>
            <div class="btn-group" role="group" aria-label="{{ __('Language') }}">
                @foreach (config('riqx.locales') as $rdCode => $rdName)
                    <a href="{{ route('riqx.locale', $rdCode) }}" lang="{{ $rdCode }}" @class(['btn', 'btn-primary' => app()->getLocale() === $rdCode, 'btn-outline-primary' => app()->getLocale() !== $rdCode])>{{ $rdName }}</a>
                @endforeach
            </div>
            <p class="text-muted fs-13 mb-0 mt-2">{{ __('Dates are shown in the Solar Hijri calendar in Persian.') }}</p>
        </div>
    </div>
</div>
