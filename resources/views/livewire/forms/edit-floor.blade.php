
<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">
            <div>
                <x-label for="floorName" value="{{ __('modules.settings.editSocietyFloor') }}" required="true"/>
                <x-input id="floorName" class="block w-full mt-1" type="text" wire:model='floorName' />
                <x-input-error for="floorName" class="mt-2" />
            </div>

            <div>
                <x-label for="towerId" :value="__('modules.settings.societyTower')" required="true"/>
                <x-select id="towerId" class="block w-full mt-1" wire:model="towerId">
                    <option value="">@lang('modules.settings.selectTower')</option>
                    @foreach ($towers as $item)
                        <option value="{{ $item->id }}">{{ $item->tower_name}}</option>
                    @endforeach
                </x-select>
                <x-input-error for="towerId" class="mt-2" />
            </div>
        </div>


        <div class="flex w-full pb-4 mt-6 space-x-4">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel  wire:click="$dispatch('hideEditFloor')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>
