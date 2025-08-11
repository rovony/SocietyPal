<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">

            <div>
                <x-label for="name" value="{{ __('modules.settings.name') }}" required="true" />
                <x-input id="name" class="block w-full mt-1" type="text" autofocus wire:model='categoryName' />
                <x-input-error for="name" class="mt-2" />
            </div>

        </div>

        <div class="flex w-full pb-4 mt-6 space-x-4">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel  wire:click="$dispatch('hideEditAssetCategory')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>

        </div>
    </form>
</div>
