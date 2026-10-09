@extends('layouts.app')

@section('title', __('Roles'))
@section('subtitle', __('Manage'))

@section('content')
    @livewire(App\Livewire\RoleList::class)
@endsection
