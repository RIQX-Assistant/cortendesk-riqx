@extends('layouts.app')

@section('title', __('Client Downloads'))
@section('subtitle', __('System'))

@section('content')
    @livewire(App\Livewire\ClientDownloadManager::class)
@endsection
