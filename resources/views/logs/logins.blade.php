@extends('layouts.app')

@section('title', __('Login Log'))
@section('subtitle', __('Logs'))

@section('content')
    @livewire(App\Livewire\LoginLogList::class)
@endsection
