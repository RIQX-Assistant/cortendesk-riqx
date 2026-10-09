@extends('layouts.app')

@section('title', __('Alarm Log'))
@section('subtitle', __('Logs'))

@section('content')
    @livewire(App\Livewire\AlarmLogList::class)
@endsection
