@extends('layouts.app')

@section('title', __('First-run setup'))

@section('content')
    @livewire(App\Livewire\SetupWizard::class)
@endsection