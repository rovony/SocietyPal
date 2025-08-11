<div>

    <div class="space-y-4">

        <div>
            <x-label for="month" value="{{ __('app.month') }}" required/>
            <x-select class="block w-full mt-1" wire:model="month" id="month">
                <option value="">{{ __('modules.rent.selectMonth') }}</option>
                @foreach($months as $month)
                    <option value="{{ $month }}">{{ ucFirst($month) }}</option>
                @endforeach
            </x-select>
            <x-input-error for="month" class="mt-2" />
        </div>

        <div>
            <x-label for="year" value="{{ __('app.year') }}" required/>
            <x-select class="block w-full mt-1" wire:model="year" id="year">
                <option value="">{{ __('modules.rent.selectYear') }}</option>
                @for ($year = now()->year; $year >= now()->year - 5; $year--)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endfor
            </x-select>
            <x-input-error for="year" class="mt-2" />
        </div>

        <div>
            <div>
                <x-label for="payment_due_date" value="{{ __('modules.maintenance.paymentDueDate') }}" required/>
                <x-datepicker  class="w-full" wire:model.live="payment_due_date" id="payment_due_date" autocomplete="off" placeholder="{{ __('modules.maintenance.paymentDueDate') }}"/>
                <x-input-error for="payment_due_date" class="mt-2" />
            </div>
        </div>

        <div>
            @foreach ($additionalCosts as $index => $cost)
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-label for="additionalCosts.{{ $index }}.title" :value="__('modules.maintenance.title')" />
                        <x-input id="additionalCosts.{{ $index }}.title" class="block w-full mt-1" type="text" wire:model="additionalCosts.{{ $index }}.title" autofocus autocomplete="off" placeholder="{{__('placeholders.additionalCostTitle')}}" />
                        <x-input-error for="additionalCosts.{{ $index }}.title" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="additionalCosts.{{ $index }}.cost" :value="__('modules.maintenance.amount')" />
                        <div class="relative inline-flex items-center mt-1 rounded-md">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <span class="text-gray-500">{{ currency() }}</span>
                            </div>
                            <x-input id="additionalCosts.{{ $index }}.cost" class="block w-full text-gray-900 rounded pl-7 placeholder:text-gray-400" type="number" wire:model="additionalCosts.{{ $index }}.cost" step="0.01"  autocomplete="off" placeholder="0.00" />
                            <x-danger-button class="ml-2" wire:click="removeAdditionalCost({{ $index }})" wire:key='remove-cost-{{ $index.rand() }}'>&cross;</x-danger-button>
                        </div>
                        <x-input-error for="additionalCosts.{{ $index }}.cost" class="mt-2" />
                    </div>
                </div>

            @endforeach

            <x-secondary-button wire:click="addAdditionalCost">@lang('modules.maintenance.addAdditionalCost')</x-secondary-button>
        </div>

    </div>

    <div class="flex w-full pb-4 mt-6 space-x-4">
        <x-button wire:click="saveAsDraft">@lang('app.saveDraft')</x-button>
        <x-button wire:click="saveAsPublish">@lang('app.savePublish')</x-button>
        <x-button-cancel wire:click="$dispatch('hideAddMaintenance')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
    </div>
</div>
