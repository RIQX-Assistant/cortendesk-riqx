@extends('layouts.app')

@section('title', __('Device'))
@section('subtitle', __('Manage'))

@section('content')
    <div class="mb-3">
        <a href="{{ route('devices') }}" class="fs-13 text-muted"><i class="ri-arrow-left-line me-1"></i>{{ __('Back to Devices') }}</a>
    </div>
    @livewire(App\Livewire\DeviceDetail::class, ['deviceId' => $device], key('device-detail-'.$device))
@endsection
