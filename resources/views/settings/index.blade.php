@extends('layouts.app')

@section('title', __('Settings'))
@section('subtitle', __('System'))

@section('content')
    @livewire(App\Livewire\SettingsPage::class)
@endsection
