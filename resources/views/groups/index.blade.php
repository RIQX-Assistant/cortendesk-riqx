@extends('layouts.app')

@section('title', __('Groups'))
@section('subtitle', __('Manage'))

@section('content')
    @livewire(App\Livewire\GroupList::class)
@endsection
