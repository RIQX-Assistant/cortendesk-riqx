@extends('layouts.app')

@section('title', __('User'))
@section('subtitle', __('Manage'))

@section('content')
    <div class="mb-3">
        <a href="{{ route('users') }}" class="fs-13 text-muted"><i class="ri-arrow-left-line me-1"></i>{{ __('Back to Users') }}</a>
    </div>
    @livewire(App\Livewire\UserDetail::class, ['userId' => $user], key('user-detail-'.$user))
@endsection
