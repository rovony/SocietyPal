<div class="card">
    <div class="w-full p-8 text-gray-800 bg-white border rounded-lg shadow-lg dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700">
        <div class="space-y-4">

            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.settings.societyTower') }}</strong>
                <span>{{ $this->apartment->towers->tower_name }}</span>
            </p>

            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.settings.floorName') }}</strong>
                <span>{{ $this->apartment->floors->floor_name }}</span>
            </p>

            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.visitorManagement.visitorName') }}</strong>
                <span>{{ $this->visitor->visitor_name }}</span>
            </p>

            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.settings.apartmentNumber') }}</strong>
                <span>{{ $this->visitor->apartment->apartment_number }}</span>
            </p>

            @if ($this->visitor->in_time && $this->visitor->date_of_visit)
                <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                    <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.visitorManagement.inTime') }} / {{ __('modules.visitorManagement.dateOfVisit') }}</strong>
                    <span>
                        {{ \Carbon\Carbon::parse($this->visitor->in_time)->timezone(timezone())->format('h:i A') }},
                        {{ \Carbon\Carbon::parse($this->visitor->date_of_visit)->timezone(timezone())->format('d F Y') }}
                    </span>
                </p>
            @endif

            @if ($this->visitor->out_time && $this->visitor->date_of_exit)
                <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                    <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.visitorManagement.outTime') }} / {{ __('modules.visitorManagement.dateOfExit') }}</strong>
                    <span>
                        {{ \Carbon\Carbon::parse($this->visitor->out_time)->timezone(timezone())->format('h:i A') }},
                        {{ \Carbon\Carbon::parse($this->visitor->date_of_exit)->timezone(timezone())->format('d F Y') }}
                    </span>
                </p>
            @endif

            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.visitorManagement.purposeOfVisit') }}</strong>
                <span>{{ $this->visitor->purpose_of_visit ?? '--' }}</span>
            </p>

            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.visitorManagement.visitorType') }}</strong>
                <span>{{ $this->visitor->visitorType->name ?? '--' }}</span>
            </p>

            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.visitorManagement.visitorAddress') }}</strong>
                <span>{{ $this->visitor->address }}</span>
            </p>
            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.serviceManagement.contactNumber') }}</strong>
                <span>
                    @if ($this->visitor->phone_number)
                        @if ($this->visitor->country_phonecode)
                            +{{ $this->visitor->country_phonecode }} {{ $this->visitor->phone_number }}
                        @else
                            {{ $this->visitor->phone_number }}
                        @endif
                    @else
                        --
                    @endif
                </span>
            </p>

            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.visitorManagement.addedBy') }}</strong>
                <span>{{ $this->visitor->user->name ?? '--' }}</span>
            </p>
            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('app.status') }}</strong>
                @if ($this->visitor->status === 'allowed')
                    <x-badge type="success">{{ ucFirst($this->visitor->status) }}</x-badge>
                @elseif ($this->visitor->status === 'not_allowed')
                    <x-badge type="danger">{{ ucFirst(str_replace('_', ' ', $this->visitor->status)) }}</x-badge>
                @else
                    <x-badge type="warning">{{ ucFirst($this->visitor->status) }}</x-badge>
                @endif
            </p>
        </div>
        <div class="relative w-full m-4 group">
            @if($this->visitor->visitor_photo_url)
                <x-label class="mb-2" value="{{ __('modules.visitorManagement.visitorPhoto') }}" />
                <div class="relative w-24 h-24 p-1 overflow-hidden rounded bg-gray-50 ring-gray-300 ring-1 dark:ring-gray-500">
                    <a href="{{ $this->visitor->visitor_photo_url }}" target="_blank">
                        <img class="object-cover w-full h-full" src="{{ $this->visitor->visitor_photo_url }}" alt="Visitor profile" />
                    </a>
                    <div class="absolute inset-0 z-10 flex items-end justify-end p-3 transition bg-black opacity-0 bg-opacity-40 group-hover:opacity-100">
                        <a href="{{ $this->visitor->visitor_photo_url }}" download class="p-2 ml-2 bg-white rounded shadow dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                <path d="M12 16l4-4h-3V4h-2v8H8l4 4zM5 20h14v-2H5v2z" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endif
        </div>
        <div class="flex justify-center mt-4 mb-6">
            <a class="min-h-[40px] w-24 rounded-xl bg-white hover:bg-gray-50 text-gray-700 border p-2 inline-flex items-center justify-center gap-1 transition-colors dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
                href="{{ route('visitors.print', $visitorId) }}" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="currentColor"
                    viewBox="0 0 16 16">
                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                    <path
                        d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                </svg>
                <span class="text-sm font-medium">@lang('app.print')</span>
            </a>
        </div>

        <div class="flex w-full pb-4 mt-6 space-x-4">
            <x-button-cancel wire:click="$dispatch('hideShowVisitor')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </div>
</div>
