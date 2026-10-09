<div class="rd-stat-grid" wire:poll.15s>

    {{-- Devices ------------------------------------------------------------ --}}
    <a href="{{ route('devices') }}" class="card text-reset card-hover" title="{{ __('View all devices') }}">
        <div class="card-body">
            <div class="rd-stat rd-tone-blue">
                <div class="rd-stat-head">
                    <span class="rd-stat-icon"><i class="ri-computer-line"></i></span>
                    <div class="min-width-0">
                        <div class="rd-stat-label">{{ __('Devices') }}</div>
                        <div class="rd-stat-value">{{ $devices }}</div>
                    </div>
                </div>
                @if ($deviceTrend > 0)
                    <div class="rd-stat-foot rd-trend-up">
                        <i class="ri-arrow-up-line"></i>{{ __(':count vs last 14 days', ['count' => $deviceTrend]) }}
                    </div>
                @elseif ($deviceTrend < 0)
                    <div class="rd-stat-foot rd-trend-down">
                        <i class="ri-arrow-down-line"></i>{{ __(':count vs last 14 days', ['count' => abs($deviceTrend)]) }}
                    </div>
                @else
                    <div class="rd-stat-foot">{{ __('No change vs last 14 days') }}</div>
                @endif
            </div>
        </div>
    </a>

    {{-- Online now --------------------------------------------------------- --}}
    <a href="{{ route('devices', ['status' => 'online']) }}" class="card text-reset card-hover" title="{{ __('View online devices') }}">
        <div class="card-body">
            <div class="rd-stat rd-tone-green">
                <div class="rd-stat-head">
                    <span class="rd-stat-icon"><i class="ri-pulse-line"></i></span>
                    <div class="min-width-0">
                        <div class="rd-stat-label">{{ __('Online now') }}</div>
                        <div class="rd-stat-value">{{ $online }}</div>
                    </div>
                </div>
                <div class="rd-stat-foot">
                    @if ($onlinePct === null)
                        {{ __('No devices enrolled yet') }}
                    @else
                        <i class="rd-dot text-success"></i>{{ __(':percent% of the fleet', ['percent' => $onlinePct]) }}
                    @endif
                </div>
            </div>
        </div>
    </a>

    {{-- Users (admin only) -------------------------------------------------- --}}
    @if ($admin)
        <a href="{{ route('users') }}" class="card text-reset card-hover" title="{{ __('Manage users') }}">
            <div class="card-body">
                <div class="rd-stat rd-tone-purple">
                    <div class="rd-stat-head">
                        <span class="rd-stat-icon"><i class="ri-group-line"></i></span>
                        <div class="min-width-0">
                            <div class="rd-stat-label">{{ __('Users') }}</div>
                            <div class="rd-stat-value">{{ $users }}</div>
                        </div>
                    </div>
                    <div class="rd-stat-foot">{{ __(':count signed in today', ['count' => $usersToday]) }}</div>
                </div>
            </div>
        </a>
    @endif

    @if ($canAudit)
    {{-- Audit-derived tiles ------------------------------------------------- --}}
    {{-- Active sessions ----------------------------------------------------- --}}
    <a href="{{ route('logs.connections') }}" class="card text-reset card-hover" title="{{ __('View connection log') }}">
        <div class="card-body">
            <div class="rd-stat rd-tone-blue">
                <div class="rd-stat-head">
                    <span class="rd-stat-icon"><i class="ri-broadcast-line"></i></span>
                    <div class="min-width-0">
                        <div class="rd-stat-label">{{ __('Sessions') }}</div>
                        <div class="rd-stat-value">{{ $sessions }}</div>
                    </div>
                </div>
                <div class="rd-stat-foot">
                    @if ($sessions > 0)
                        <i class="rd-dot text-success"></i>{{ __('Live now') }}
                    @else
                        {{ __('None in progress') }}
                    @endif
                </div>
            </div>
        </div>
    </a>

    {{-- Connections today ---------------------------------------------------- --}}
    {{-- Label kept short so it survives the six-across breakpoint; "vs yesterday"
         in the footer is what pins the value to today. --}}
    <a href="{{ route('logs.connections') }}" class="card text-reset card-hover" title="{{ __('Connections started today — view connection log') }}">
        <div class="card-body">
            <div class="rd-stat rd-tone-teal">
                <div class="rd-stat-head">
                    <span class="rd-stat-icon"><i class="ri-links-line"></i></span>
                    <div class="min-width-0">
                        <div class="rd-stat-label">{{ __('Connections') }}</div>
                        <div class="rd-stat-value">{{ $connectionsToday }}</div>
                    </div>
                </div>
                @if ($connectionTrend > 0)
                    <div class="rd-stat-foot rd-trend-up">
                        <i class="ri-arrow-up-line"></i>{{ __(':count vs yesterday', ['count' => $connectionTrend]) }}
                    </div>
                @elseif ($connectionTrend < 0)
                    <div class="rd-stat-foot rd-trend-down">
                        <i class="ri-arrow-down-line"></i>{{ __(':count vs yesterday', ['count' => abs($connectionTrend)]) }}
                    </div>
                @else
                    <div class="rd-stat-foot">{{ __('Same as yesterday') }}</div>
                @endif
            </div>
        </div>
    </a>

    {{-- Alarms --------------------------------------------------------------- --}}
    <a href="{{ route('logs.alarms') }}" class="card text-reset card-hover" title="{{ __('View alarm log') }}">
        <div class="card-body">
            <div class="rd-stat {{ $alarms24h > 0 ? 'rd-tone-red' : 'rd-tone-amber' }}">
                <div class="rd-stat-head">
                    <span class="rd-stat-icon"><i class="ri-alarm-warning-line"></i></span>
                    <div class="min-width-0">
                        <div class="rd-stat-label">{{ __('Alarms (24h)') }}</div>
                        <div class="rd-stat-value">{{ $alarms24h }}</div>
                    </div>
                </div>
                <div class="rd-stat-foot">
                    @if ($alarms24h > 0)
                        {{ __('Latest :time', ['time' => $lastAlarmAt?->diffForHumans(short: true)]) }}
                    @else
                        <i class="rd-dot text-success"></i>{{ __('All clear') }}
                    @endif
                </div>
            </div>
        </div>
    </a>
    @endif
</div>
