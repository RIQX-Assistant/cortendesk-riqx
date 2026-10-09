@extends('layouts.app')

@section('title', __('Console Audit'))
@section('subtitle', __('Logs'))

@section('content')
    @livewire(App\Livewire\ConsoleAuditList::class)
@endsection
