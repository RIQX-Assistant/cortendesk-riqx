@extends('layouts.app')

@section('title', __('Connection Log'))
@section('subtitle', __('Logs'))

@section('content')
    @livewire(App\Livewire\ConnectionLog::class)
@endsection
