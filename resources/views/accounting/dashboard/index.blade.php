@extends('layouts.app')

@section('title', __('accountant.dashboard_title'))
@section('page-title', __('accountant.dashboard_title'))

@section('content')
<div class="rounded-2xl bg-white p-6 shadow-sm">
    <h2 class="text-2xl font-extrabold text-secondary">{{ __('accountant.dashboard_title') }}</h2>
    <p class="mt-2 text-gray-600">{{ __('accountant.hero.subtitle') }}</p>
    <div class="mt-6">
        <a href="{{ auth()->user()->hasRole('ACCOUNTANT') ? route('accountant.dashboard') : route('accounting.journal.index') }}" class="inline-flex rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white">{{ __('general.dashboard') }}</a>
    </div>
</div>
@endsection
