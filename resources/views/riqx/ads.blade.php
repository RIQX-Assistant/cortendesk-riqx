@extends('layouts.app')

@section('title', __('Ads'))
@section('subtitle', __('System'))

@section('content')
    @livewire(App\Livewire\RiqxAdManager::class)
@endsection
