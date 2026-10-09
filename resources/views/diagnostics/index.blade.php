@extends('layouts.app')

@section('title', __('Fleet diagnostics'))

@section('content')
    {{-- The page title comes from the layout; this row carries the description
         and the export button only. --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <p class="text-muted mb-0">{{ __('Bounded probes and fleet signals. No test email is sent automatically.') }}</p>
        <a href="{{ route('diagnostics.export') }}" class="btn btn-sm btn-outline-light">
            <i class="ri-download-2-line me-1"></i>{{ __('Sanitized JSON') }}
        </a>
    </div>

    @php
        $checks = [
            ['label' => __('Application'), 'ok' => $report['application']['ok'], 'detail' => __('CortenDesk v:version', ['version' => $report['application']['version']])],
            ['label' => __('Database'), 'ok' => $report['database']['ok'], 'detail' => $report['database']['ok'] ? __('Query completed.') : __('Query failed.')],
            ['label' => __('ID server'), 'ok' => $report['services']['id_server']['ok'], 'detail' => $report['services']['id_server']['configured'] ? __(':host on port :port', ['host' => $report['services']['id_server']['host_label'], 'port' => $report['services']['id_server']['port']]) : __('Not configured.')],
            ['label' => __('Relay server'), 'ok' => $report['services']['relay_server']['ok'], 'detail' => $report['services']['relay_server']['configured'] ? __(':host on port :port', ['host' => $report['services']['relay_server']['host_label'], 'port' => $report['services']['relay_server']['port']]) : __('Not configured.')],
            ['label' => __('Public API'), 'ok' => $report['services']['api']['ok'], 'detail' => __('Local version route :route is registered.', ['route' => $report['services']['api']['version_route']])],
            ['label' => __('WebSocket bridge'), 'ok' => $report['services']['websocket_bridge']['ok'], 'detail' => __($report['services']['websocket_bridge']['note'])],
            ['label' => __('Scheduler'), 'ok' => $report['scheduler']['ok'], 'detail' => $report['scheduler']['ok'] ? __('Heartbeat is fresh.') : __('No fresh scheduler heartbeat.')],
            ['label' => __('ID server link'), 'ok' => $report['server_link']['ok'], 'detail' => __($report['server_link']['note'])
                .($report['server_link']['agent'] ? ' ('.$report['server_link']['agent'].')' : '')
                .($report['server_link']['mode'] === 'approved' ? ' '.__('Server access: approved devices only.') : '')],
            ['label' => 'SMTP', 'ok' => $report['smtp']['configured'] ? $report['smtp']['healthy'] : null, 'detail' => __($report['smtp']['note'])],
        ];
    @endphp

    <div class="row g-3">
        @foreach ($checks as $check)
            @php
                $status = $check['ok'] === null ? 'unknown' : ($check['ok'] ? 'ok' : 'failed');
                $tone = $status === 'ok' ? 'success' : ($status === 'failed' ? 'danger' : 'secondary');
            @endphp
            <div class="col-md-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between gap-2">
                            <strong>{{ $check['label'] }}</strong>
                            <span class="badge bg-{{ $tone }}-subtle text-{{ $tone }}">{{ __(ucfirst($status)) }}</span>
                        </div>
                        <p class="text-muted fs-13 mb-0 mt-2">{{ $check['detail'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card mt-3">
        <div class="card-header"><h5 class="card-title mb-0">{{ __('Fleet summary') }}</h5></div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                @foreach (['total' => __('Devices'), 'online' => __('Online'), 'offline' => __('Offline'), 'silent_over_24h' => __('Silent 24h+'), 'pending' => __('Pending'), 'possible_duplicates' => __('Possible duplicates')] as $key => $label)
                    <div class="col-6 col-lg">
                        <div class="text-muted fs-13">{{ $label }}</div>
                        <div class="fs-4 fw-semibold">
                            @if ($key === 'possible_duplicates' && $report['fleet'][$key] > 0)
                                <a href="{{ route('devices', ['status' => 'duplicates']) }}" title="{{ __('List them on the Devices page') }}">{{ $report['fleet'][$key] }}</a>
                            @else
                                {{ $report['fleet'][$key] }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="text-muted fs-13">
                {{ __('Version status is relative to the newest client currently reporting in this fleet, not a vendor release feed.') }}
            </p>

            @if ($report['fleet']['versions'])
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>{{ __('Client version') }}</th><th>{{ __('Devices') }}</th><th>{{ __('Status') }}</th></tr></thead>
                        <tbody>
                            @foreach ($report['fleet']['versions'] as $version)
                                <tr>
                                    <td class="rd-mono">{{ $version['version'] }}</td>
                                    <td>{{ $version['count'] }}</td>
                                    <td>{{ __($version['status']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">{{ __('No client versions have been reported.') }}</p>
            @endif
        </div>
    </div>
@endsection
