<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">

            <div>
                <x-label for="apartmentType" value="{{ __('modules.settings.societyApartment') }}" required="true" />
                <x-input id="apartmentType" class="block w-full mt-1" type="text" autofocus wire:model='apartmentType' />
                <x-input-error for="apartmentType" class="mt-2" />
            </div>

        </div>

        <div class="flex w-full pb-4 mt-6 space-x-4">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel  wire:click="$dispatch('hideAddApartment')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>

        </div>
    </form>
</div>
