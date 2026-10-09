@extends('layouts.app')

@section('title', __('Address Books'))
@section('subtitle', __('Manage'))

@section('content')
    @livewire(App\Livewire\AddressBookManager::class)
@endsection
