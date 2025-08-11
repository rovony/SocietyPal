@extends('layouts.app')

@section('content')

<div class="p-4 bg-white block  dark:bg-gray-800 dark:border-gray-700">
    @include('dashboard.update-message-dashboard')

    <x-cron-message  :showModal="false" :modal="true"/>
    <div id="notification-alert">
        <x-alert type="warning" icon="info-circle">
            <div class="flex items-center gap-2">
                @lang('messages.pushNotificationMessage')
                <x-button id="subscribe-button">@lang('messages.enableNotifications')</x-button>
            </div>
        </x-alert>
    </div>

    <div class="flex justify-between">
        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">@lang('menu.dashboard')</h1>
        <div class="inline-flex items-center gap-1 dark:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-event" viewBox="0 0 16 16">
                <path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/>
                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
            </svg>

            {{ now()->timezone(timezone())->format('l, d M, h:i A') }}
        </div>
    </div>

</div>

<x-banner />

<div class="bg-white block  dark:bg-gray-800 dark:border-gray-700">

    <div class="col-span-2 p-4 py-2">
        <div class="grid w-full grid-cols-1 gap-4 xl:grid-cols-4">
            @if(user_can('Show Tower'))
                @livewire('dashboard.total-tower-count')
            @endif

            @if(user_can('Show Apartment') && isRole() != 'Guard')
                @livewire('dashboard.total-unsold-apartment-count')
            @endif

            @if(user_can('Show Apartment') || isRole() == 'Owner' || isRole() == 'Tenant')
                @livewire('dashboard.total-apartment-count')
            @endif

            @if (user_can('Show Maintenance') || isRole() == 'Owner' || isRole() == 'Tenant')
                @livewire('dashboard.total-maintenance-dues-count')
            @endif

            @if(user_can('Show Owner'))
                @livewire('dashboard.total-owner-count')
            @endif

            @if(user_can('Show Tenant') || isRole() == 'Owner')
                @livewire('dashboard.total-tenant-count')
            @endif

        </div>
    </div>

    <div class="grid w-full grid-cols-1 md:grid-cols-2 gap-4 py-2 p-4 mb-20">

        @if (user_can('Show Book Amenity') || isRole() == 'Owner' || isRole() == 'Tenant')
            @livewire('dashboard.today-booking-amenities-count')
        @endif

        @if (user_can('Show Rent') || isRole() == 'Owner' || isRole() == 'Tenant')
            @livewire('dashboard.rents')
        @endif

        @if (user_can('Show Tickets') || isRole() == 'Owner' || isRole() == 'Tenant' || isRole() == 'Guard')
            @livewire('dashboard.tickets')
        @endif

        @if (user_can('Show Utility Bills')|| isRole() == 'Owner' || isRole() == 'Tenant')
            @livewire('dashboard.utility-bills')
        @endif

        @if (user_can('Show Common Area Bills'))
            @livewire('dashboard.common-area-bills')
        @endif

        @if (user_can('Show Service Time Logging') || isRole() == 'Guard')
            @livewire('dashboard.today-service-clock-in-out')
        @endif

        @if (user_can('Show Visitors')|| isRole() == 'Owner' || isRole() == 'Tenant' || isRole() == 'Guard')
            @livewire('dashboard.total-visitor-count')
        @endif

        @if (user_can('Show Notice Board') || isRole() == 'Owner' || isRole() == 'Tenant' || isRole() == 'Guard')
            @livewire('dashboard.notice-board')
        @endif

    </div>

</div>
@endsection
