<div class="rd-sidebar">

    <a href="{{ route('overview') }}" class="rd-sidebar-logo" dir="ltr">
        <span class="rd-logo-lg">
            <img src="{{ \App\Support\Brand::url('wordmark-dark-bg.png') }}" alt="{{ config('app.name') }}" height="30"/>
        </span>
        <span class="rd-logo-sm">
            <img src="{{ \App\Support\Brand::url('mark.png') }}" alt="{{ config('app.name') }}" height="40" class="mx-auto d-block"/>
        </span>
    </a>

    {{-- RIQX: the collapse/expand control sits on the column it moves (the
         top-bar button only remains for phones, where this column is hidden). --}}
    <button type="button" class="riqx-rail-toggle" data-rd-toggle="sidebar" aria-label="{{ __('Toggle menu') }}" title="{{ __('Toggle menu') }}">
        <i class="ri-sidebar-fold-line riqx-rail-fold" aria-hidden="true"></i>
        <i class="ri-sidebar-unfold-line riqx-rail-unfold" aria-hidden="true"></i>
    </button>

    <div class="rd-sidebar-close" data-rd-dismiss="sidebar" role="button" aria-label="{{ __('Close menu') }}">
        <i class="ri-close-fill align-middle"></i>
    </div>

    <div class="rd-sidebar-scroll">

        @php
            // Delegated roles (PLAN D4). consoleAllows() returns true for
            // is_admin and, for a user with no role, for exactly the areas a
            // non-admin could always reach — so this sidebar renders
            // identically on an install with no roles defined.
            $u = auth()->user();
            $canDevices = $u?->consoleAllows('device');
            $canAddressBooks = $u?->consoleAllows('address_book');
            $canGroups = $u?->consoleAllows('group');
            $canUsers = $u?->consoleAllows('user');
            $canStrategies = $u?->consoleAllows('strategy');
            $canAudit = $u?->consoleAllows('audit');
            $canAuditManage = $u?->consoleAllows('audit', 'rw');
            $canSettings = $u?->consoleAllows('setting');
            $canRoles = $u?->is_admin;
        @endphp

        <ul class="rd-nav">

            <li class="rd-nav-item {{ request()->routeIs('overview') ? 'rd-current' : '' }}">
                <a href="{{ route('overview') }}" class="rd-nav-link {{ request()->routeIs('overview') ? 'active' : '' }}">
                    <i class="ri-home-4-line"></i>
                    <span> {{ __('Overview') }} </span>
                </a>
            </li>

            @if ($canDevices || $canAddressBooks || $canGroups || $canUsers || $canStrategies || $canRoles)
                <li class="rd-nav-title">{{ __('Manage') }}</li>
            @endif

            @if ($canDevices)
                <li class="rd-nav-item {{ request()->routeIs('devices') ? 'rd-current' : '' }}">
                    <a href="{{ route('devices') }}" class="rd-nav-link {{ request()->routeIs('devices') ? 'active' : '' }}">
                        <i class="ri-computer-line"></i>
                        <span> {{ __('Devices') }} </span>
                    </a>
                </li>
            @endif

            @if ($canAddressBooks)
                <li class="rd-nav-item {{ request()->routeIs('address-books') ? 'rd-current' : '' }}">
                    <a href="{{ route('address-books') }}" class="rd-nav-link {{ request()->routeIs('address-books') ? 'active' : '' }}">
                        <i class="ri-contacts-book-2-line"></i>
                        <span> {{ __('Address Books') }} </span>
                    </a>
                </li>
            @endif

            @if (config('cortendesk.native_webclient'))
                <li class="rd-nav-item">
                    <a href="{{ route('webclient') }}" target="cortendesk-webclient" rel="noopener" class="rd-nav-link">
                        <i class="ri-global-line"></i>
                        <span> {{ __('Web Client') }} </span>
                    </a>
                </li>
            @elseif (config('cortendesk.webclient_url'))
                <li class="rd-nav-item">
                    <a href="{{ config('cortendesk.webclient_url') }}" target="_blank" rel="noopener" class="rd-nav-link">
                        <i class="ri-global-line"></i>
                        <span> {{ __('Web Client') }} </span>
                    </a>
                </li>
            @endif

            @if ($canGroups)
                <li class="rd-nav-item {{ request()->routeIs('groups') ? 'rd-current' : '' }}">
                    <a href="{{ route('groups') }}" class="rd-nav-link {{ request()->routeIs('groups') ? 'active' : '' }}">
                        <i class="ri-group-line"></i>
                        <span> {{ __('Groups') }} </span>
                    </a>
                </li>
            @endif

            @if ($canUsers)
                <li class="rd-nav-item {{ request()->routeIs('users') ? 'rd-current' : '' }}">
                    <a href="{{ route('users') }}" class="rd-nav-link {{ request()->routeIs('users') ? 'active' : '' }}">
                        <i class="ri-user-settings-line"></i>
                        <span> {{ __('Users') }} </span>
                    </a>
                </li>
            @endif

            {{-- Roles are super-admin only: a delegated admin who could edit
                 roles could grant themselves anything (PLAN D4). --}}
            @if ($canRoles)
                <li class="rd-nav-item {{ request()->routeIs('roles') ? 'rd-current' : '' }}">
                    <a href="{{ route('roles') }}" class="rd-nav-link {{ request()->routeIs('roles') ? 'active' : '' }}">
                        <i class="ri-shield-user-line"></i>
                        <span> {{ __('Roles') }} </span>
                    </a>
                </li>
            @endif

            @if ($canStrategies)
                <li class="rd-nav-item {{ request()->routeIs('strategies') ? 'rd-current' : '' }}">
                    <a href="{{ route('strategies') }}" class="rd-nav-link {{ request()->routeIs('strategies') ? 'active' : '' }}">
                        <i class="ri-shield-keyhole-line"></i>
                        <span> {{ __('Strategies') }} </span>
                    </a>
                </li>
            @endif

            @if ($canAudit)
                <li class="rd-nav-title">{{ __('Monitor') }}</li>

                <li class="rd-nav-item {{ request()->routeIs('logs.*') ? 'rd-current' : '' }}">
                    <a data-bs-toggle="collapse" href="#sidebarLogs" aria-expanded="{{ request()->routeIs('logs.*') ? 'true' : 'false' }}" aria-controls="sidebarLogs" class="rd-nav-link">
                        <i class="ri-file-list-3-line"></i>
                        <span> {{ __('Logs') }} </span>
                        <span class="rd-nav-arrow"></span>
                    </a>
                    <div class="collapse {{ request()->routeIs('logs.*') ? 'show' : '' }}" id="sidebarLogs">
                        <ul class="rd-nav-sub">
                            <li class="{{ request()->routeIs('logs.connections') ? 'rd-current' : '' }}"><a href="{{ route('logs.connections') }}">{{ __('Connections') }}</a></li>
                            <li class="{{ request()->routeIs('logs.file-transfers') ? 'rd-current' : '' }}"><a href="{{ route('logs.file-transfers') }}">{{ __('File Transfers') }}</a></li>
                            <li class="{{ request()->routeIs('logs.alarms') ? 'rd-current' : '' }}"><a href="{{ route('logs.alarms') }}">{{ __('Alarms') }}</a></li>
                            @if ($canAuditManage)
                                <li class="{{ request()->routeIs('logs.logins') ? 'rd-current' : '' }}"><a href="{{ route('logs.logins') }}">{{ __('Logins') }}</a></li>
                                <li class="{{ request()->routeIs('logs.console') ? 'rd-current' : '' }}"><a href="{{ route('logs.console') }}">{{ __('Console') }}</a></li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            @if ($canSettings)
                <li class="rd-nav-title">{{ __('System') }}</li>

                <li class="rd-nav-item {{ request()->routeIs('settings') ? 'rd-current' : '' }}">
                    <a href="{{ route('settings') }}" class="rd-nav-link {{ request()->routeIs('settings') ? 'active' : '' }}">
                        <i class="ri-settings-3-line"></i>
                        <span> {{ __('Settings') }} </span>
                    </a>
                </li>

                <li class="rd-nav-item {{ request()->routeIs('diagnostics*') ? 'rd-current' : '' }}">
                    <a href="{{ route('diagnostics') }}" class="rd-nav-link {{ request()->routeIs('diagnostics*') ? 'active' : '' }}">
                        <i class="ri-pulse-line"></i>
                        <span> {{ __('Diagnostics') }} </span>
                    </a>
                </li>

                <li class="rd-nav-item {{ request()->routeIs('client-downloads') ? 'rd-current' : '' }}">
                    <a href="{{ route('client-downloads') }}" class="rd-nav-link {{ request()->routeIs('client-downloads') ? 'active' : '' }}">
                        <i class="ri-download-cloud-line"></i>
                        <span> {{ __('Client Downloads') }} </span>
                    </a>
                </li>

                {{-- RIQX: ads shown in the RIQX app --}}
                <li class="rd-nav-item {{ request()->routeIs('riqx.ads') ? 'rd-current' : '' }}">
                    <a href="{{ route('riqx.ads') }}" class="rd-nav-link {{ request()->routeIs('riqx.ads') ? 'active' : '' }}">
                        <i class="ri-advertisement-line"></i>
                        <span> {{ __('Ads') }} </span>
                    </a>
                </li>

                @php
                    $rdgenUrl = \App\Models\Setting::get('rdgen_url', config('cortendesk.rdgen_url'));
                @endphp
                @if ($rdgenUrl)
                    <li class="rd-nav-item">
                        <a href="{{ $rdgenUrl }}" target="_blank" rel="noopener" class="rd-nav-link">
                            <i class="ri-install-line"></i>
                            <span> {{ __('Build Installers') }} </span>
                        </a>
                    </li>
                @endif
            @endif
        </ul>

    </div>

    @if ($canSettings)
        @php
            $rdUpgrade = \App\Support\UpdateChecker::upgradeAvailable();
            // The console has no health probe of its own, so the status line
            // reports the one signal that is real — the release check — and
            // says so rather than claiming anything about the servers.
            $rdChecked = \App\Support\UpdateChecker::latestVersion() !== null;
        @endphp
        <div class="rd-sidebar-version">
            {{-- With the update check off there is nothing to report. --}}
            @if (\App\Support\UpdateChecker::enabled())
            <span class="rd-sidebar-status {{ $rdUpgrade ? 'rd-sidebar-status-warn' : ($rdChecked ? 'rd-sidebar-status-ok' : 'rd-sidebar-status-unknown') }}"
                  title="{{ __('CortenDesk compares the running version against the latest published release.') }}">
                <i class="rd-dot rd-dot-lg"></i>
                <span class="rd-sidebar-status-label">
                    @if ($rdUpgrade)
                        {{ __('Update available') }}
                    @elseif ($rdChecked)
                        {{ __('Running the latest release') }}
                    @else
                        {{ __('Release check unavailable') }}
                    @endif
                </span>
            </span>
            @endif
            <span class="rd-sidebar-version-num">v{{ config('cortendesk.api_version') }}</span>
            {{-- The badge only appears when it carries something the status
                 line above does not: the way to act on an upgrade. --}}
            @if ($rdUpgrade)
                <a href="{{ \App\Support\UpdateChecker::releaseNotesUrl($rdUpgrade) }}" target="_blank" rel="noopener"
                   class="rd-shell-badge"
                   title="{{ __('Version :version is available. Opens the release notes.', ['version' => $rdUpgrade]) }}"><i class="ri-download-cloud-2-line"></i>{{ __('Update to v:version', ['version' => $rdUpgrade]) }}</a>
            @endif
        </div>
    @endif
</div>
