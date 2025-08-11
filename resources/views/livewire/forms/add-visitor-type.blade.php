<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">

            <div>
                <x-label for="visitorTypeName" value="{{ __('modules.settings.societyVisitorType') }}" required="true" />
                <x-input id="visitorTypeName" class="block w-full mt-1" type="text" autofocus wire:model='visitorTypeName' />
                <x-input-error for="visitorTypeName" class="mt-2" />
            </div>

        <div class="flex w-full pb-4 mt-6 space-x-4">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel  wire:click="$dispatch('hideAddVisitorType')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>

        </div>
    </form>
</div>
