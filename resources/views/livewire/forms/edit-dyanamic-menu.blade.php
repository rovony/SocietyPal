<div>
    <form wire:submit="editDynamicMenu" x-data="{ content: @entangle('editMenuContent').live }">
        <div class="space-y-4">
            <div>
                <x-label for="editMenuName" value="{{ __('modules.settings.menuName') }}" />
                <x-input id="editMenuName" class="block w-full mt-1" type="text"
                    placeholder="{{ __('placeholders.menuNamePlaceHolder') }}" wire:model.live="editMenuName" wire:keyup="generateSlug" required />
                <x-input-error for="editMenuName" class="mt-2" />
            </div>

            <div>
                <x-label for="editMenuSlug" value="{{ __('modules.settings.menuSlug') }}" />
                <x-input id="editMenuSlug" class="block w-full mt-1" type="text"
                    placeholder="{{ __('placeholders.menuSlugPlaceHolder') }}" wire:model="editMenuSlug" required />
                <x-input-error for="editMenuSlug" class="mt-2" />
            </div>

            <div>
                <x-label for="editMenuContent" value="{{ __('modules.settings.menuContent') }}" />

                <input x-ref="editMenuContent" id="editMenuContent" name="editMenuContent" wire:model="editMenuContent"
                    value="{{ $editMenuContent }}" type="hidden" />

                <div wire:ignore>
                    <trix-editor class="text-sm trix-content" input="editMenuContent" data-gramm="false"
                        x-on:trix-change="$wire.editMenuContent = $event.target.value" x-ref="trixEditor"></trix-editor>
                </div>

                <x-input-error for="editMenuContent" class="mt-2" />
            </div>

            <div class="flex w-full pb-4 mt-6 space-x-4">
                <x-button>@lang('app.save')</x-button>

            </div>
        </div>
    </form>
</div>
