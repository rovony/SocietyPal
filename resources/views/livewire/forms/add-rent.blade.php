<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">
            <div class="flex space-x-4">
                <div class="w-1/2">
                    <x-label for="tower_id" :value="__('modules.settings.selectTower')" required="true" />
                    <x-select id="tower_id" class="block w-full mt-1" wire:model.live="tower_id">
                        <option value="">@lang('modules.settings.selectTower')</option>
                        @foreach ($towers as $tower)
                            <option value="{{ $tower->id }}">{{ $tower->tower_name }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error for="tower_id" class="mt-2" />
                </div>

                <div class="w-1/2">
                    <x-label for="floor_id" :value="__('modules.settings.selectFloor')" required="true" />
                    <x-select id="floor_id" class="block w-full mt-1" wire:model.live="floor_id">
                        <option value="">@lang('modules.settings.selectFloor')</option>
                        @foreach ($floors as $floor)
                            <option value="{{ $floor->id }}">{{ $floor->floor_name }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error for="floor_id" class="mt-2" />
                </div>
            </div>

            <div class="mt-4">
                <x-label for="apartment_id" :value="__('modules.settings.apartmentNumber')" required="true" />
                <x-select id="apartment_id" class="mt-1 block w-full" wire:model.live="apartment_id">
                    <option value="">@lang('modules.settings.apartmentNumber')</option>
                    @foreach ($apartments as $apartment)
                        <option value="{{ $apartment->id }}">{{ $apartment->apartment_number }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="apartment_id" class="mt-2" />
            </div>

            <div>
                <x-label for="tenant_id" value="{{ __('modules.rent.tenantName') }}" required/>
                <x-input id="tenant_id" class="mt-1 block w-full text-gray-900 dark:text-white" type="text"
                    value="{{ $tenant ? $tenant->user->name : '' }}" 
                    placeholder="{{ $tenant_id ? '' : __('modules.rent.tenantName') }}"
                    disabled/>
            </div>

        @if($tenant_id)
            <div>
                <x-label for="rent_for_year" value="{{ __('modules.rent.rentForYear') }}" required/>
                <x-select class="mt-1 block w-full" wire:model="rent_for_year" id="rent_for_year">
                    @for ($year = now()->year; $year >= now()->year - 5; $year--)
                        <option value="{{ $year }}" {{ $rent_for_year == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endfor
                </x-select>
            </div>

            @if($billing_cycle !== 'annually')
                <div>
                    <x-label for="rent_for_month" value="{{ __('modules.rent.rentForMonth') }}" required/>
                    <x-select class="mt-1 block w-full" wire:model="rent_for_month" id="rent_for_month">
                        @foreach($months as $month)
                            <option value="{{ $month }}" {{ $rent_for_month ===$month ? 'selected' : '' }}>
                                {{ __('modules.rent.' . strtolower($month)) }}
                            </option>
                        @endforeach
                    </x-select>
                    <x-input-error for="rent_for_month" class="mt-2" />
                </div>
            @endif
        @endif

        <div>
            <x-label for="rent_amount" value="{{ __('modules.tenant.rentAmount') }}" required/>
            <x-input id="rent_amount" class="block mt-1 w-full" type="number" step="0.01" wire:model='rent_amount' autocomplete="off"/>
            <x-input-error for="rent_amount" class="mt-2" />
        </div>

        <div>
            <x-label for="status" value="{{ __('modules.settings.status') }}" required/>
            <x-select id="status" class="block w-full mt-1" wire:model.live="status">
                <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>{{ __('modules.rent.paid') }}</option>
                <option value="unpaid" {{ $status == 'unpaid' ? 'selected' : '' }}>{{ __('modules.rent.unpaid') }}</option>
            </x-select>
            <x-input-error for="status" class="mt-2" />
        </div>

            @if ($status === 'paid')
                <div>
                    <x-label for="payment_date" value="{{ __('modules.rent.paymentDate') }}"/>
                    <x-datepicker wire:change='loadAvailableTimeSlots' class="w-full" wire:model.live="payment_date" id="payment_date" autocomplete="off" placeholder="{{ __('modules.rent.paymentDate') }}" :maxDate="true"/>
                </div>

                <div x-data="{ photoName: null, photoPreview: null }" x-on:photo-removed.window="photoName = null; photoPreview = null;">
                    <input type="file" id="profilePhoto" class="hidden" wire:model="profilePhoto" accept="image/png, image/gif, image/jpeg, image/webp"
                        x-ref="profilePhoto"
                        x-on:change="
                            photoName = $refs.profilePhoto.files[0].name;
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                photoPreview = e.target.result;
                            };
                            reader.readAsDataURL($refs.profilePhoto.files[0]);
                        "/>
                    <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.profilePhoto.click()">
                        {{ __('modules.utilityBills.paymentProof') }}
                    </x-secondary-button>

                    <div class="mt-2" x-show="photoPreview">
                        <span class="block w-20 h-20 bg-center bg-no-repeat bg-cover rounded-full"
                            x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                        </span>
                    </div>

                    <div class="mt-2" x-show="photoPreview">
                        <x-danger-button type="button" wire:click="removeProfilePhoto">
                            {{ __('modules.utilityBills.removePaymentProof') }}
                        </x-danger-button>
                    </div>

                    <x-input-error for="profilePhoto" class="mt-2" />
                </div>
            @endif

            <div class="flex w-full pb-4 space-x-4 mt-6">
                <x-button>@lang('app.save')</x-button>
                <x-button-cancel  wire:click="$dispatch('hideAddRent')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
            </div>
        </div>
    </form>
</div>
