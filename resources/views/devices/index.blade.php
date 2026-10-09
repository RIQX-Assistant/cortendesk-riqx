@extends('layouts.app')

@section('title', __('Devices'))
@section('subtitle', __('Manage'))

@section('content')
    @livewire(App\Livewire\DeviceList::class)
@endsection
