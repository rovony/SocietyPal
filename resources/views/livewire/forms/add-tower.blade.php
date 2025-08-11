<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">

            <div>
                <x-label for="towerName" value="{{ __('modules.settings.societyTower') }}" required="true" />
                <x-input id="towerName" class="block w-full mt-1" type="text" wire:model='towerName' />
                <x-input-error for="towerName" class="mt-2" />
            </div>

        </div>

        <div class="flex w-full pb-4 mt-6 space-x-4">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel  wire:click="$dispatch('hideAddTower')" wire:key="towerCancel" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>

        </div>
    </form>
</div>
