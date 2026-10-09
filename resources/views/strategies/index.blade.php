@extends('layouts.app')

@section('title', __('Strategies'))
@section('subtitle', __('Manage'))

@section('content')
    @livewire(App\Livewire\StrategyList::class)
@endsection
