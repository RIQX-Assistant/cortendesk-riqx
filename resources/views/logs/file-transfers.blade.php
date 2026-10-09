@extends('layouts.app')

@section('title', __('File Transfer Log'))
@section('subtitle', __('Logs'))

@section('content')
    @livewire(App\Livewire\FileTransferLog::class)
@endsection
