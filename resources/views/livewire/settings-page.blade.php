<div>
    @php $canManageSettings = auth()->user()?->consoleAllows('setting', 'rw'); @endphp

    @unless ($canManageSettings)
        <div class="alert alert-info py-2">
            <i class="ri-eye-line me-1"></i>{{ __('You have view-only access to settings. Changes are disabled.') }}
        </div>
    @endunless

    @php
        $mailSvc = app(\App\Services\MailSettings::class);
        $mailBroken = \App\Support\LoginEmailVerification::isActive() && ! $mailSvc->isHealthy();
    @endphp

    @if ($mailBroken || session('mail_broken'))
        <div class="alert alert-danger">
            <i class="ri-mail-close-line me-1"></i><strong>{{ __('Email is failing.') }}</strong>
            {{ session('mail_broken') ?: __('Nobody else can sign in while sign-in verification is enabled. Fix the settings below and send a test message.') }}
            {{-- SMTP errors routinely carry a long unbroken token (a host, a URL, a
                 certificate subject). .rd-mono breaks anywhere, so it cannot set a
                 min-content wider than the phone and drag the body sideways. --}}
            @if ($mailError = $mailSvc->lastError())
                <div class="mt-2 rd-mono">{{ $mailError }}</div>
            @endif
        </div>
    @endif

    @if ($saved)
        <div class="alert alert-success py-2" wire:poll.4s="$set('saved', false)">
            <i class="ri-check-line me-1"></i>{{ __('Settings saved.') }}
        </div>
    @endif

    {{-- Nothing was written if any field failed — say so where it can be seen.
         save() also jumps $tab to the failing field, since one giant validate()
         covers every tab and the error may otherwise render off-screen. --}}
    @if ($errors->any())
        <div class="alert alert-danger py-2">
            <i class="ri-error-warning-line me-1"></i>{!! __('Settings were :status — fix the highlighted field below.', ['status' => '<strong>'.e(__('not saved')).'</strong>']) !!}
        </div>
    @endif

    {{-- Tab nav. Active tab lives in the $tab Livewire prop (survives save re-renders). --}}
    @php
        $tabs = [
            'server'      => ['ri-server-line', __('Server')],
            'client'      => ['ri-download-2-line', __('Client Setup')],
            'security'    => ['ri-shield-keyhole-line', __('Security')],
            'sso'         => ['ri-shield-user-line', 'SSO'],
            'email'       => ['ri-mail-send-line', __('Email')],
            'notifications' => ['ri-notification-3-line', __('Notifications')],
            'maintenance' => ['ri-database-2-line', __('Maintenance')],
        ];
    @endphp
    {{-- The label stays visible at every width. Six tabs do not fit across a
         390px screen, but .rd-tabbar is a horizontal scroller (section 17 of
         cortendesk.css) so the strip slides instead of wrapping — and six
         unlabelled icons, two of them shields, are not a navigation. Livewire
         morphs this list rather than replacing it, so the scroll position
         survives a tab change. --}}
    <ul class="nav nav-tabs nav-bordered rd-tabbar mb-3">
        @foreach ($tabs as $key => [$icon, $label])
            <li class="nav-item">
                <a href="#" wire:click.prevent="$set('tab', '{{ $key }}')"
                   class="nav-link {{ $tab === $key ? 'active' : '' }}">
                    <i class="{{ $icon }} me-1"></i><span>{{ $label }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    <div class="tab-content">

        {{-- ============================ SERVER ============================ --}}
        <div class="tab-pane {{ $tab === 'server' ? 'show active' : '' }}">
            <div class="row">
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('RustDesk Server') }}</h5>
                        </div>
                        <div class="card-body">
                            <form wire:submit="save">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('ID Server (hbbs)') }}</label>
                                    <input type="text" class="form-control" wire:model="idServer" placeholder="{{ __('e.g. :example', ['example' => 'hbbs.example.com:21116']) }}">
                                    <div class="form-text">{{ __('Host:port of the rendezvous server clients register with.') }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Relay Server (hbbr)') }}</label>
                                    <input type="text" class="form-control" wire:model="relayServer" placeholder="{{ __('e.g. :example', ['example' => 'hbbs.example.com:21117']) }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Public Key') }}</label>
                                    <input type="text" class="form-control rd-mono" wire:model="publicKey" placeholder="{{ __('contents of :file', ['file' => 'id_ed25519.pub']) }}">
                                    <div class="form-text">{!! __("The server's ed25519 public key — clients need it when :flag is enabled.", ['flag' => '<code>ENCRYPTED_ONLY</code>']) !!}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Build Installers URL') }}</label>
                                    <input type="text" class="form-control @error('rdgenUrl') is-invalid @enderror"
                                           wire:model="rdgenUrl" placeholder="https://rdgen.crayoneater.org">
                                    @error('rdgenUrl') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="form-text">{!! __("Opened by the sidebar's :entry entry — point it at your own rdgen instance. Leave empty to hide the menu entry.", ['entry' => '<strong>'.e(__('Build Installers')).'</strong>']) !!}</div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="downloadsOnLogin"
                                               wire:model="downloadsOnLogin">
                                        <label class="form-check-label" for="downloadsOnLogin">{{ __('Link to client downloads on the sign-in page') }}</label>
                                    </div>
                                    <div class="form-text">{!! __('Adds a link beneath the sign-in form when builds are published under :downloads. The :page page stays reachable either way — unpublish a build to withdraw it.', ['downloads' => '<a href="'.e(route('client-downloads')).'">'.e(__('Client Downloads')).'</a>', 'page' => '<a href="'.e(route('downloads.index')).'" target="_blank" rel="noopener">/downloads</a>']) !!}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Online window (seconds)') }}</label>
                                    <input type="number" class="form-control @error('onlineWindow') is-invalid @enderror"
                                           wire:model="onlineWindow" min="20" max="600" style="max-width: 140px;">
                                    @error('onlineWindow') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="form-text">{{ __('Seconds without a heartbeat before a device shows as offline. Clients report every ~15s.') }}</div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="requireDeviceApproval"
                                               wire:model="requireDeviceApproval">
                                        <label class="form-check-label" for="requireDeviceApproval">{{ __('Require device approval') }}</label>
                                    </div>
                                    <div class="form-text">{!! __('When on, a newly seen device is held as :pending and hidden from the console and API until an operator approves it on the :tab tab. Deployed devices (via an API token) are pre-approved.', ['pending' => '<strong>'.e(__('Pending')).'</strong>', 'tab' => e(__('Devices')).' &rarr; '.e(__('Pending'))]) !!}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="serverAccessMode">{{ __('Server access') }}</label>
                                    <select id="serverAccessMode" class="form-select @error('serverAccessMode') is-invalid @enderror"
                                            wire:model="serverAccessMode" style="max-width: 320px;">
                                        <option value="{{ \App\Services\ServerLink::MODE_OPEN }}">{{ __('Open: any device with the key') }}</option>
                                        <option value="{{ \App\Services\ServerLink::MODE_APPROVED }}">{{ __('Approved devices only') }}</option>
                                    </select>
                                    @error('serverAccessMode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="form-text">{{ __('Enforced by the ID server. With approved devices only, pending, recycled and unknown devices still come online and show as Pending, but cannot start or receive sessions, and only clients signed in to this console (and the web client) can start one. Turn on Require device approval as well, or every device that reports here is approved on arrival. Incoming-only devices are refused when they start a session in either mode.') }}</div>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="serverIpMatch" wire:model="serverIpMatch">
                                        <label class="form-check-label" for="serverIpMatch">{{ __('Identify signed-out devices by IP address') }}</label>
                                    </div>
                                    <div class="form-text">{{ __("Approved devices only: lets a signed-out client start sessions when an approved device registered from the same IP. Weak: devices sharing a public IP, or behind a proxy that rewrites source addresses (Docker's userland proxy, IPv6 port publishing, Docker Desktop), can pass as each other.") }}</div>
                                    @php $linkTone = $serverLink['ok'] === true ? 'text-success' : ($serverLink['ok'] === false ? 'text-danger' : 'text-muted'); @endphp
                                    <div class="form-text {{ $linkTone }}" id="server-link-status">
                                        <i class="{{ $serverLink['ok'] === false ? 'ri-error-warning-line' : 'ri-link' }} me-1"></i>{{ __('ID server link: :note', ['note' => __($serverLink['note'])]) }}
                                        @if ($serverLink['ok'] === false) {{ __('Needs CortenDesk Server 1.1.0 or later linked to this console.') }} @endif
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary" @disabled(! $canManageSettings)><i class="ri-save-line me-1"></i>{{ __('Save Settings') }}</button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Relay Servers') }}</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted fs-13 mb-3">{!! __("The relay pool (hbbr) your rendezvous server hands to clients. Relay selection is decided by hbbs at connect time — the console stores this list so it can manage the server's :config config. Geo tags are for your own documentation. Leave empty to use the single :fallback above as the fallback.", ['config' => '<code>relay-servers</code>', 'fallback' => '<strong>'.e(__('Relay Server (hbbr)')).'</strong>']) !!}</p>

                            <form wire:submit="save">
                                {{-- Address takes the whole first line on a phone; the geo tag and
                                     the remove button share the second. The old split put the button
                                     in a col-1, which is a 17px content box at 390px against the
                                     ~45px a button needs — it wrapped under the inputs. col-sm-auto
                                     sizes the button to itself instead of to a twelfth of the row,
                                     so it stays honest at every width. --}}
                                @forelse ($relayServers as $i => $relay)
                                    <div class="row g-2 mb-2 align-items-start" wire:key="relay-{{ $i }}">
                                        <div class="col-12 col-sm">
                                            <input type="text" class="form-control @error('relayServers.'.$i.'.address') is-invalid @enderror"
                                                   wire:model="relayServers.{{ $i }}.address" placeholder="relay.example.com:21117">
                                            @error('relayServers.'.$i.'.address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-8 col-sm-4">
                                            <input type="text" class="form-control @error('relayServers.'.$i.'.geo') is-invalid @enderror"
                                                   wire:model="relayServers.{{ $i }}.geo" placeholder="{{ __('Geo tag') }}">
                                            @error('relayServers.'.$i.'.geo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-4 col-sm-auto d-grid">
                                            <button type="button" class="btn btn-light text-danger" wire:click="removeRelay({{ $i }})"
                                                    title="{{ __('Remove relay') }}" @disabled(! $canManageSettings)><i class="ri-close-line"></i></button>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted fs-13 fst-italic mb-2">{{ __('No relay servers configured — using the single relay fallback above.') }}</p>
                                @endforelse

                                <div class="d-flex gap-2 mt-3">
                                    <button type="button" class="btn btn-light" wire:click="addRelay" @disabled(! $canManageSettings)>
                                        <i class="ri-add-line me-1"></i>{{ __('Add relay') }}
                                    </button>
                                    <button type="submit" class="btn btn-primary" @disabled(! $canManageSettings)><i class="ri-save-line me-1"></i>{{ __('Save Settings') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================= CLIENT SETUP ========================= --}}
        <div class="tab-pane {{ $tab === 'client' ? 'show active' : '' }}">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Client Setup') }}</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted fs-13">{!! __('Point RustDesk clients at this console under :path:', ['path' => '<strong>'.e(__('Settings → Network → ID/Relay Server')).'</strong>']) !!}</p>

                            <label class="form-label fs-13 text-muted mb-0">{{ __('ID Server') }}</label>
                            <div class="input-group input-group-sm mb-2">
                                <input type="text" class="form-control rd-mono" readonly value="{{ $idServer }}">
                                <button class="btn btn-light" type="button" onclick="rdCopyPrevious(this)"><i class="ri-file-copy-line"></i></button>
                            </div>

                            <label class="form-label fs-13 text-muted mb-0">{{ __('Relay Server') }}</label>
                            <div class="input-group input-group-sm mb-2">
                                <input type="text" class="form-control rd-mono" readonly value="{{ $relayServer }}">
                                <button class="btn btn-light" type="button" onclick="rdCopyPrevious(this)"><i class="ri-file-copy-line"></i></button>
                            </div>

                            <label class="form-label fs-13 text-muted mb-0">{{ __('API Server') }}</label>
                            <div class="input-group input-group-sm mb-2">
                                <input type="text" class="form-control rd-mono" readonly value="{{ $apiUrl }}">
                                <button class="btn btn-light" type="button" onclick="rdCopyPrevious(this)"><i class="ri-file-copy-line"></i></button>
                            </div>

                            <label class="form-label fs-13 text-muted mb-0">{{ __('Key') }}</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control rd-mono" readonly value="{{ $publicKey }}">
                                <button class="btn btn-light" type="button" onclick="rdCopyPrevious(this)"><i class="ri-file-copy-line"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =========================== SECURITY =========================== --}}
        <div class="tab-pane {{ $tab === 'security' ? 'show active' : '' }}">
            <div class="row">
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Two-Factor Authentication') }}</h5>
                        </div>
                        <div class="card-body">
                            <form wire:submit="save">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="twoFactorRequired"
                                               wire:model="twoFactorRequired">
                                        <label class="form-check-label" for="twoFactorRequired">{{ __('Require two-factor authentication for all users') }}</label>
                                    </div>
                                    <div class="form-text">{{ __("Signed-in users without 2FA are sent to the setup screen and can't use the console until they enroll.") }}</div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="twoFactorRequiredAdmins"
                                               wire:model="twoFactorRequiredAdmins" @if($twoFactorRequired) disabled @endif>
                                        <label class="form-check-label" for="twoFactorRequiredAdmins">{{ __('Require two-factor authentication for administrators') }}</label>
                                    </div>
                                    <div class="form-text">{{ __('Enforce 2FA for admin accounts only. Superseded when the all-users switch above is on.') }}</div>
                                </div>

                                {{-- Email sign-in verification sits beside 2FA because it is the
                                     same kind of policy; the relay itself is configured on the Email tab. --}}
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="emailLoginVerification"
                                               wire:model="emailLoginVerification" @unless($mailEnabled) disabled @endunless>
                                        <label class="form-check-label" for="emailLoginVerification">{{ __('Require an emailed code on new devices') }}</label>
                                    </div>
                                    <div class="form-text">
                                        @if ($mailEnabled)
                                            {{ __('Users without an authenticator app get a 6-digit code by email the first time they sign in from a browser this console has not seen. The browser stays trusted for :days days.', ['days' => $emailTrustedDeviceDays]) }}
                                            {{ __('Accounts with 2FA enrolled are unaffected — they answer the authenticator instead.') }}
                                            @if ($usersWithoutEmail > 0)
                                                <span class="text-warning d-block mt-1">
                                                    {{ __(':count active account(s) have no email address and are let through with a password alone. An SMTP outage does the same, deliberately, so a broken relay cannot lock everyone out.', ['count' => $usersWithoutEmail]) }}
                                                </span>
                                            @else
                                                <span class="d-block mt-1">{{ __('An account with no email address, or an SMTP outage, lets the sign-in through on the password alone — a broken relay must not lock everyone out.') }}</span>
                                            @endif
                                        @else
                                            {!! __('Configure and enable a mail relay on the :email tab first — without one there is no way to deliver the code.', ['email' => '<a href="#" wire:click.prevent="$set(\'tab\', \'email\')">'.e(__('Email')).'</a>']) !!}
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">{{ __('Remember a verified browser for (days)') }}</label>
                                    <input type="number" class="form-control @error('emailTrustedDeviceDays') is-invalid @enderror"
                                           wire:model="emailTrustedDeviceDays" min="1" max="365" style="max-width: 140px;">
                                    @error('emailTrustedDeviceDays') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="form-text">{{ __('Trust is revoked immediately by "Force logout", disabling the account, or a password change.') }}</div>
                                </div>

                                <button type="submit" class="btn btn-primary" @disabled(! $canManageSettings)><i class="ri-save-line me-1"></i>{{ __('Save Settings') }}</button>
                            </form>
                        </div>
                    </div>

                    {{-- API tokens live under Security — stable key keeps it mounted across saves --}}
                    {{-- Nested component: a 403 in the child kills the whole Settings page,
                         so a role with settings but no token access simply does not render it. --}}
                    @if (auth()->user()?->consoleAllows('token', 'r'))
                        @livewire(App\Livewire\ApiTokenManager::class, [], 'api-tokens')
                    @endif
                </div>
            </div>
        </div>

        {{-- ============================= SSO ============================= --}}
        <div class="tab-pane {{ $tab === 'sso' ? 'show active' : '' }}">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ __('Single Sign-On (OIDC)') }}</h5>
                            @if ($oidcEnabled)
                                <span class="badge bg-success-subtle text-success">{{ __('Enabled') }}</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">{{ __('Disabled') }}</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <p class="text-muted fs-13">
                                {{ __('Let operators sign in with your identity provider — Keycloak, Authentik, Entra ID, Okta, Google Workspace or anything else that speaks OpenID Connect.') }}
                                {{ __('Register this callback URL with your provider:') }}
                            </p>
                            <div class="mb-3">
                                <input type="text" class="form-control form-control-sm rd-mono"
                                       value="{{ $oidcCallbackUrl }}" readonly onfocus="this.select()">
                            </div>

                            <form wire:submit="save">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="oidcEnabled"
                                               wire:model="oidcEnabled">
                                        <label class="form-check-label" for="oidcEnabled">{{ __('Enable single sign-on') }}</label>
                                    </div>
                                    <div class="form-text">{{ __('Adds a sign-in button to the login page.') }}</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">{{ __('Provider URL') }}</label>
                                    <input type="text" class="form-control @error('oidcDiscoveryUrl') is-invalid @enderror"
                                           wire:model="oidcDiscoveryUrl" placeholder="https://idp.example.com/realms/main">
                                    @error('oidcDiscoveryUrl') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="form-text">
                                        {!! __('The issuer URL. :path is appended automatically, or paste the full discovery URL.', ['path' => '<code>/.well-known/openid-configuration</code>']) !!}
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ __('Client ID') }}</label>
                                        <input type="text" class="form-control" wire:model="oidcClientId">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ __('Client Secret') }}</label>
                                        <input type="password" class="form-control" wire:model="oidcClientSecret"
                                               autocomplete="new-password"
                                               placeholder="{{ $oidcClientSecretSet ? __('Stored — leave blank to keep') : __('Required') }}">
                                        <div class="form-text">{{ __('Encrypted at rest. Blank leaves the stored secret unchanged.') }}</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="testOidc"
                                            wire:loading.attr="disabled" wire:target="testOidc" @disabled(! $canManageSettings)>
                                        <i class="ri-plug-line me-1"></i>
                                        <span wire:loading.remove wire:target="testOidc">{{ __('Test Connection') }}</span>
                                        <span wire:loading wire:target="testOidc">{{ __('Contacting provider…') }}</span>
                                    </button>
                                    @if ($oidcTestMessage)
                                        <div class="mt-2 alert {{ $oidcTestOk ? 'alert-success' : 'alert-danger' }} py-2 mb-0 fs-13">
                                            {{ $oidcTestMessage }}
                                        </div>
                                    @endif
                                </div>

                                <hr>

                                <div class="mb-3">
                                    <label class="form-label">{{ __('New users') }}</label>
                                    <select class="form-select" wire:model="oidcNewUserPolicy">
                                        <option value="deny">{{ __('Deny — only existing console accounts may sign in') }}</option>
                                        <option value="pending">{{ __('Create, pending administrator approval') }}</option>
                                        <option value="active">{{ __('Create and allow in immediately') }}</option>
                                    </select>
                                    <div class="form-text">
                                        {{ __("Applies when the provider's identity matches no existing account. An email address is only ever matched to an existing account when the provider marks it verified.") }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="oidcRequireVerifiedEmail"
                                               wire:model="oidcRequireVerifiedEmail">
                                        <label class="form-check-label" for="oidcRequireVerifiedEmail">{{ __('Require the provider to assert email verification') }}</label>
                                    </div>
                                    <div class="form-text">
                                        {!! __("Off by default, because the claim is optional in OpenID Connect and common providers make it meaningless — Microsoft Entra ID never sends it, and Authentik sends :value for everyone. Accounts are matched on the provider's stable user id, not the address, so this changes little in practice. Turn it on only if you federate with a provider whose users can choose their own email address.", ['value' => '<code>false</code>']) !!}
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ __('Default group for new users') }}</label>
                                        <select class="form-select" wire:model="oidcDefaultGroupId">
                                            <option value="0">{{ __('None') }}</option>
                                            @foreach ($userGroups as $group)
                                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ __('Allowed email domains') }}</label>
                                        <input type="text" class="form-control" wire:model="oidcAllowedDomains"
                                               placeholder="example.com, example.org">
                                        <div class="form-text">{{ __('Blank allows any domain the provider authenticates.') }}</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="oidcDefaultAdmin"
                                               wire:model="oidcDefaultAdmin">
                                        <label class="form-check-label" for="oidcDefaultAdmin">{{ __('Make new SSO users administrators') }}</label>
                                    </div>
                                    <div class="form-text text-warning">
                                        {{ __('Leave off unless every account your provider authenticates should administer this console.') }}
                                    </div>
                                </div>

                                <hr>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="oidcLogoutEnabled"
                                               wire:model="oidcLogoutEnabled">
                                        <label class="form-check-label" for="oidcLogoutEnabled">{{ __('Sign out of the provider too') }}</label>
                                    </div>
                                    <div class="form-text">{{ __('Ends the provider session on logout, not just the console session.') }}</div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="oidcDisableLocalLogin"
                                               wire:model="oidcDisableLocalLogin">
                                        <label class="form-check-label" for="oidcDisableLocalLogin">{{ __('Disable password sign-in') }}</label>
                                    </div>
                                    <div class="form-text">
                                        {{ __('Hides the password form so SSO is the only way in. It takes effect only while SSO is enabled and configured, so a provider outage restores password sign-in by itself.') }}
                                        {!! __(':setting in :file forces it off from the shell.', ['setting' => '<code>CORTENDESK_OIDC_DISABLED=true</code>', 'file' => '<code>.env</code>']) !!}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">{{ __('Advanced') }}</label>
                                    <input type="text" class="form-control mb-2" wire:model="oidcScopes" placeholder="openid email profile">
                                    <div class="form-text mb-2">{{ __('Scopes requested from the provider.') }}</div>
                                    <input type="text" class="form-control mb-2" wire:model="oidcButtonLabel" placeholder="{{ __('Sign in with SSO') }}">
                                    <div class="form-text mb-2">{{ __('Label for the login-page button.') }}</div>
                                    <input type="text" class="form-control" wire:model="oidcPublicBaseUrl"
                                           placeholder="https://idp.example.com">
                                    <div class="form-text">
                                        {{ __("Browser-facing provider URL. Only needed when the console reaches the provider on a different address than your users' browsers do (container networks, split DNS).") }}
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary" @disabled(! $canManageSettings)><i class="ri-save-line me-1"></i>{{ __('Save Settings') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================ EMAIL ============================ --}}
        <div class="tab-pane {{ $tab === 'email' ? 'show active' : '' }}">
            <div class="row">
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ __('Outbound Email (SMTP)') }}</h5>
                            @if ($smtpEnabled)
                                <span class="badge bg-success-subtle text-success">{{ __('Enabled') }}</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">{{ __('Disabled') }}</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <p class="text-muted fs-13">
                                {{ __('Used for user invitations and emailed sign-in codes. Any SMTP relay works — your own server, a provider, or a local smarthost. Messages are sent as the console handles the request, so keep the relay close and responsive.') }}
                            </p>

                            <form wire:submit="save">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="smtpEnabled"
                                               wire:model="smtpEnabled">
                                        <label class="form-check-label" for="smtpEnabled">{{ __('Enable outbound email') }}</label>
                                    </div>
                                    <div class="form-text">{{ __('Off means nothing is ever sent — invitation links must then be copied by hand.') }}</div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label">{{ __('SMTP host') }}</label>
                                        <input type="text" class="form-control @error('smtpHost') is-invalid @enderror"
                                               wire:model="smtpHost" placeholder="smtp.example.com">
                                        @error('smtpHost') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">{{ __('Port') }}</label>
                                        <input type="number" class="form-control @error('smtpPort') is-invalid @enderror"
                                               wire:model="smtpPort" min="1" max="65535">
                                        @error('smtpPort') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">{{ __('Encryption') }}</label>
                                    <select class="form-select" wire:model="smtpEncryption" style="max-width: 420px;">
                                        <option value="starttls">{{ __('STARTTLS (usually port 587)') }}</option>
                                        <option value="ssl">{{ __('Implicit TLS / SSL (usually port 465)') }}</option>
                                        <option value="none">{{ __('None (STARTTLS still used if the server offers it)') }}</option>
                                    </select>
                                    <div class="form-text">
                                        {{ __('The mail library upgrades a plain connection whenever the relay advertises STARTTLS, so "None" cannot force a genuinely unencrypted session.') }}
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ __('Username') }}</label>
                                        <input type="text" class="form-control @error('smtpUsername') is-invalid @enderror"
                                               wire:model="smtpUsername" autocomplete="off" placeholder="{{ __('Optional') }}">
                                        @error('smtpUsername') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ __('Password') }}</label>
                                        <input type="password" class="form-control @error('smtpPassword') is-invalid @enderror"
                                               wire:model="smtpPassword" autocomplete="new-password"
                                               placeholder="{{ $smtpPasswordSet ? __('Stored — leave blank to keep') : __('Optional') }}">
                                        @error('smtpPassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        <div class="form-text">{{ __('Encrypted at rest. Blank leaves the stored password unchanged.') }}</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ __('From address') }}</label>
                                        <input type="text" class="form-control @error('smtpFromAddress') is-invalid @enderror"
                                               wire:model="smtpFromAddress" placeholder="console@example.com">
                                        @error('smtpFromAddress') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        <div class="form-text">{{ __('Must be an address your relay is allowed to send as (SPF/DMARC).') }}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ __('From name') }}</label>
                                        <input type="text" class="form-control @error('smtpFromName') is-invalid @enderror"
                                               wire:model="smtpFromName" placeholder="{{ config('app.name') }}">
                                        @error('smtpFromName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary" @disabled(! $canManageSettings)><i class="ri-save-line me-1"></i>{{ __('Save Settings') }}</button>
                            </form>

                            <hr>

                            <label class="form-label">{{ __('Send a test email') }}</label>
                            <div class="row g-2 align-items-start">
                                <div class="col-sm-7">
                                    <input type="email" class="form-control" wire:model="smtpTestTo" placeholder="you@example.com">
                                </div>
                                <div class="col-sm-5 d-grid">
                                    <button type="button" class="btn btn-outline-secondary" wire:click="sendTestEmail"
                                            wire:loading.attr="disabled" wire:target="sendTestEmail" @disabled(! $canManageSettings)>
                                        <i class="ri-send-plane-line me-1"></i>
                                        <span wire:loading.remove wire:target="sendTestEmail">{{ __('Send test email') }}</span>
                                        <span wire:loading wire:target="sendTestEmail">{{ __('Sending…') }}</span>
                                    </button>
                                </div>
                            </div>
                            <div class="form-text">{!! __('Uses the :saved settings, not what is typed above — save first.', ['saved' => '<strong>'.e(__('saved')).'</strong>']) !!}</div>
                            @if ($smtpTestMessage)
                                <div class="mt-2 alert {{ $smtpTestOk ? 'alert-success' : 'alert-danger' }} py-2 mb-0 fs-13">
                                    {{ $smtpTestMessage }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================== NOTIFICATIONS ======================== --}}
        <div class="tab-pane {{ $tab === 'notifications' ? 'show active' : '' }}">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">{{ __('Apprise notifications') }}</h5></div>
                        <div class="card-body">
                            <p class="text-muted fs-13">{{ __('Send fleet and security events through a self-hosted Apprise API. Destination credentials are encrypted and never shown again.') }}</p>
                            <form wire:submit="save">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="appriseEnabled" wire:model="appriseEnabled">
                                    <label class="form-check-label" for="appriseEnabled">{{ __('Enable notifications') }}</label>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Apprise API endpoint') }}</label>
                                    <input type="url" class="form-control @error('appriseEndpoint') is-invalid @enderror" wire:model="appriseEndpoint"
                                           autocomplete="off" data-1p-ignore data-lpignore="true"
                                           placeholder="{{ $appriseEndpointSet ? __('Stored — leave blank to keep') : 'https://apprise.example.com' }}">
                                    @error('appriseEndpoint')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="form-text">{!! __('Base URL only. CortenDesk appends :path.', ['path' => '<code>/notify</code>']) !!}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Delivery mode') }}</label>
                                    <select class="form-select" wire:model.live="appriseMode">
                                        <option value="config">{{ __('Saved Apprise configuration key') }}</option>
                                        <option value="urls">{{ __('Stateless Apprise URLs') }}</option>
                                    </select>
                                </div>
                                @if ($appriseMode === 'config')
                                    <div class="mb-3"><label class="form-label">{{ __('Configuration key') }}</label><input type="password" class="form-control" wire:model="appriseConfigKey"
                                               autocomplete="new-password" data-1p-ignore data-lpignore="true"
                                               placeholder="{{ $appriseConfigKeySet ? __('Stored — leave blank to keep') : 'office-alerts' }}"></div>
                                @else
                                    <div class="mb-3"><label class="form-label">{{ __('Apprise URLs') }}</label><textarea class="form-control rd-mono" rows="4" wire:model="appriseUrls"
                                                       autocomplete="off" data-1p-ignore data-lpignore="true"
                                                       placeholder="{{ $appriseUrlsSet ? __('Stored — leave blank to keep') : __('One Apprise URL per line') }}"></textarea><div class="form-text">{{ __('Write-only and encrypted at rest.') }}</div></div>
                                @endif
                                <div class="mb-3"><label class="form-label">{{ __('Cooldown (minutes)') }}</label><input type="number" class="form-control" style="max-width: 140px" min="0" max="1440" wire:model="appriseCooldownMinutes"></div>
                                <div class="mb-3"><label class="form-label">{{ __('Offline alert grace (minutes)') }}</label><input type="number" class="form-control @error('appriseOfflineGraceMinutes') is-invalid @enderror" style="max-width: 140px" min="0" max="1440" wire:model="appriseOfflineGraceMinutes"><div class="form-text">{{ __('Wait this long after a device becomes offline before sending its Apprise alert.') }}</div>@error('appriseOfflineGraceMinutes')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="row">
                                    @foreach ($appriseEventLabels as $event => $label)
                                        @php $eventKey = str_replace('.', '_', $event); @endphp
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="event-{{ str_replace('.', '-', $event) }}" wire:model="appriseEvents.{{ $eventKey }}">
                                                <label class="form-check-label" for="event-{{ str_replace('.', '-', $event) }}">{{ __($label) }}</label>
                                            </div>
                                            @if (str_starts_with($event, 'device.'))
                                                <select class="form-select form-select-sm mt-2" wire:model.live="appriseScopes.{{ $eventKey }}">
                                                    <option value="all">{{ __('All approved devices') }}</option>
                                                    <option value="selected">{{ __('Selected groups or devices') }}</option>
                                                </select>
                                                @if (($appriseScopes[$eventKey] ?? 'all') === 'selected')
                                                    <select class="form-select form-select-sm mt-2" multiple wire:model="appriseScopeGroups.{{ $eventKey }}" aria-label="{{ __('Groups for :event', ['event' => __($label)]) }}">
                                                        @foreach ($appriseDeviceGroups as $group)<option value="{{ $group->id }}">{{ __('Group: :name', ['name' => $group->name]) }}</option>@endforeach
                                                    </select>
                                                    <select class="form-select form-select-sm mt-2" multiple wire:model="appriseScopeDevices.{{ $eventKey }}" aria-label="{{ __('Devices for :event', ['event' => __($label)]) }}">
                                                        @foreach ($appriseDevices as $device)<option value="{{ $device->id }}">{{ $device->alias ?: ($device->hostname ?: $device->rustdesk_id) }}</option>@endforeach
                                                    </select>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary mt-2" type="submit" @disabled(! $canManageSettings)><i class="ri-save-line me-1"></i>{{ __('Save settings') }}</button>
                                <button class="btn btn-outline-secondary mt-2" type="button" wire:click="sendTestNotification" @disabled(! $canManageSettings)>{{ __('Send test') }}</button>
                                @if ($appriseTestMessage)<div class="alert {{ $appriseTestOk ? 'alert-success' : 'alert-danger' }} py-2 mt-3 mb-0">{{ $appriseTestMessage }}</div>@endif
                            </form>
                        </div>
                    </div>
                    @if (auth()->user()?->is_admin)
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">{{ __('Presence alert maintenance') }}</h5></div>
                        <div class="card-body">
                            <p class="text-muted fs-13">{{ __('Temporarily suppress offline and recovery notifications for one device or an entire group. Security and login alerts are not affected.') }}</p>
                            <form wire:submit="createPresenceSnooze" class="row g-2 align-items-end">
                                <div class="col-md-3"><label class="form-label">{{ __('Apply to') }}</label><select class="form-select" wire:model.live="presenceSnoozeTargetType"><option value="device">{{ __('Device') }}</option><option value="group">{{ __('Device group') }}</option></select></div>
                                <div class="col-md-5"><label class="form-label">{{ __('Target') }}</label><select class="form-select @error('presenceSnoozeTargetId') is-invalid @enderror" wire:model="presenceSnoozeTargetId"><option value="0">{{ __('Choose a target') }}</option>@if($presenceSnoozeTargetType === 'group')@foreach($presenceSnoozeGroups as $group)<option value="{{ $group->id }}">{{ $group->name }}</option>@endforeach @else @foreach($presenceSnoozeDevices as $device)<option value="{{ $device->id }}">{{ $device->alias ?: ($device->hostname ?: $device->rustdesk_id) }}</option>@endforeach @endif</select></div>
                                <div class="col-md-2"><label class="form-label">{{ __('Minutes') }}</label><input type="number" class="form-control @error('presenceSnoozeMinutes') is-invalid @enderror" min="15" max="10080" wire:model="presenceSnoozeMinutes"></div>
                                <div class="col-md-2"><button class="btn btn-outline-warning w-100" type="submit" @disabled(! $canManageSettings)>{{ __('Snooze alerts') }}</button></div>
                            </form>
                            @error('presenceSnoozeTargetId')<div class="text-danger fs-13 mt-2">{{ $message }}</div>@enderror @error('presenceSnoozeMinutes')<div class="text-danger fs-13 mt-2">{{ $message }}</div>@enderror
                            <div class="mt-3">
                                @forelse($presenceSnoozes as $snooze)
                                    @php $target = $snooze->target_type === 'group' ? $presenceSnoozeGroups->firstWhere('id', $snooze->target_id) : $presenceSnoozeDevices->firstWhere('id', $snooze->target_id); @endphp
                                    <div class="d-flex justify-content-between align-items-center border-top py-2"><div><strong>{{ $snooze->target_type === 'group' ? __('Group') : __('Device') }}: {{ $target?->name ?: $target?->alias ?: $target?->hostname ?: $target?->rustdesk_id ?: __('Deleted target') }}</strong><div class="text-muted fs-13">{{ __('Alerts muted until :time (:relative)', ['time' => \App\Support\LocalDate::long($snooze->expires_at), 'relative' => $snooze->expires_at->diffForHumans()]) }}</div></div><button type="button" class="btn btn-sm btn-outline-secondary" wire:click="clearPresenceSnooze({{ $snooze->id }})" @disabled(! $canManageSettings)>{{ __('End now') }}</button></div>
                                @empty
                                    <div class="text-muted fs-13">{{ __('No active presence-alert maintenance windows.') }}</div>
                                @endforelse
                            </div>
                        </div>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">{{ __('Recent deliveries') }}</h5></div>
                        <div class="card-body">
                            <p class="text-muted fs-13">{{ __('A send that times out, cannot connect, or gets HTTP 429, 424 (a destination such as Slack failed) or 5xx from Apprise is retried by the scheduler about 1 and 5 minutes later, 3 attempts in all. Other HTTP errors are not retried, and neither is anything older than 30 minutes. A retry is dropped when a newer event replaces it, such as an offline alert after the device came back.') }}</p>
                            @forelse ($notificationDeliveries as $delivery)
                                <div class="d-flex justify-content-between gap-3 border-bottom py-2">
                                    @php $rdDevice = $notificationDeliveryDevices[$delivery->id] ?? null; @endphp
                                    <div><strong>{{ $delivery->title }}</strong>
                                        <div class="text-muted fs-13">
                                            {{ __(\App\Services\AppriseNotifications::EVENTS[$delivery->event] ?? $delivery->event) }}
                                            @if ($rdDevice)
                                                · <a href="{{ route('devices.show', $rdDevice->id) }}">{{ trim((string) ($rdDevice->alias ?: $rdDevice->hostname)) ?: __('Device') }} ({{ $rdDevice->rustdesk_id }})</a>
                                            @endif
                                            · <span title="{{ \App\Support\LocalDate::dateTimeSeconds($delivery->created_at) }}">{{ $delivery->created_at?->diffForHumans() }}</span>
                                        </div>
                                        @if($delivery->error)<div class="text-danger fs-13 text-break">{{ $delivery->error }}</div>@endif
                                        @php $maxAttempts = \App\Services\AppriseNotifications::MAX_ATTEMPTS; @endphp
                                        @if ($delivery->retryPending())
                                            <div class="text-muted fs-13">{{ __('Attempt :attempt of :max · next retry', ['attempt' => $delivery->attempts, 'max' => $maxAttempts]) }} <span title="{{ \App\Support\LocalDate::dateTimeSeconds($delivery->next_retry_at) }}">{{ $delivery->next_retry_at->diffForHumans() }}</span></div>
                                        @elseif ($delivery->status === 'superseded')
                                            <div class="text-muted fs-13">{{ __('Attempt :attempt of :max · retry dropped, a newer event replaced it', ['attempt' => $delivery->attempts, 'max' => $maxAttempts]) }}</div>
                                        @elseif ($delivery->attempts > 1)
                                            <div class="text-muted fs-13">{{ $delivery->status === 'sent' ? __('Sent on attempt :attempt of :max', ['attempt' => $delivery->attempts, 'max' => $maxAttempts]) : __('Failed after attempt :attempt of :max', ['attempt' => $delivery->attempts, 'max' => $maxAttempts]) }}</div>
                                        @endif
                                    </div>
                                    @php $tone = $delivery->retryPending() ? 'warning' : (['sent' => 'success', 'failed' => 'danger'][$delivery->status] ?? 'secondary'); @endphp
                                    <span class="badge bg-{{ $tone }}-subtle text-{{ $tone }} align-self-start">{{ __($delivery->retryPending() ? 'retrying' : $delivery->status) }}</span>
                                </div>
                            @empty
                                <p class="text-muted mb-0">{{ __('No delivery attempts yet.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================= MAINTENANCE ========================= --}}
        <div class="tab-pane {{ $tab === 'maintenance' ? 'show active' : '' }}">
            <div class="row">
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Log Retention') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Log retention (days)') }}</label>
                                <input type="number" class="form-control @error('logRetentionDays') is-invalid @enderror"
                                       wire:model="logRetentionDays" min="0" max="3650" style="max-width: 140px;">
                                @error('logRetentionDays') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">{{ __('Connection, file-transfer, login and alarm logs older than this are deleted nightly.') }} <strong>{{ __('0 keeps logs forever.') }}</strong></div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary" wire:click="save" @disabled(! $canManageSettings)><i class="ri-save-line me-1"></i>{{ __('Save') }}</button>
                                <button type="button" class="btn btn-light" wire:click="pruneNow"
                                        wire:confirm="{{ __('Delete all log entries older than :days days now?', ['days' => $logRetentionDays]) }}"
                                        @if($logRetentionDays < 1 || ! $canManageSettings) disabled @endif>
                                    <i class="ri-delete-bin-6-line me-1"></i>{{ __('Prune now') }}
                                </button>
                            </div>
                            @if ($pruneResult)
                                {{-- pre-line keeps the line breaks the pruner emits; rd-mono adds the
                                     break-anywhere guard, since the counts are printed per table name. --}}
                                <div class="alert alert-info py-2 mt-2 mb-0 rd-mono" style="white-space: pre-line;">{{ $pruneResult }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('About') }}</h5>
                        </div>
                        <div class="card-body">
                            <dl class="rd-deflist">
                                <div class="rd-def"><dt>{{ __('CortenDesk') }}</dt><dd class="rd-mono">v{{ config('cortendesk.api_version') }}</dd></div>
                                <div class="rd-def"><dt>Laravel</dt><dd class="rd-mono">{{ app()->version() }}</dd></div>
                                <div class="rd-def"><dt>PHP</dt><dd class="rd-mono">{{ PHP_VERSION }}</dd></div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
