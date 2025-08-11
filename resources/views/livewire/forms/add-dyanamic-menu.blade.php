<div>
    <form wire:submit.prevent="submitDynamicWebPageForm">

        <x-help-text class="mb-6">@lang('modules.settings.addMoreWebPageHelp')</x-help-text>

        <div class="space-y-4">
            <div>
                <x-label for="menu_name" value="{{ __('modules.settings.menuName') }}" />
                <x-input id="menu_name" class="block w-full mt-1" type="text"
                    wire:model.live="menuName" wire:keyup="generateSlug" required />
                <x-input-error for="menu_name" class="mt-2" />
                @error('menuName')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <x-label for="menu_slug" value="{{ __('modules.settings.menuSlug') }}" />
                <x-input id="menu_slug" class="block w-full mt-1" type="text"
                   wire:model="menuSlug" required />
                <x-input-error for="menu_slug" class="mt-2" />
                @error('menuSlug')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <x-label for="menuContent" value="{{ __('modules.settings.menuContent') }}" />

                <input x-ref="menuContent" id="menuContent" name="menuContent" wire:model="menuContent"
                    value="{{ $menuContent }}" type="hidden" />

                <div wire:ignore class="mt-2">
                    <trix-editor class="text-sm trix-content" input="menuContent" data-gramm="false"
                        x-on:trix-change="$wire.set('menuContent', $event.target.value)" x-ref="trixEditor"  x-init="
                            window.addEventListener('reset-trix-editor', () => {
                                $refs.trixEditor.editor.loadHTML('');
                            });" >
                    </trix-editor>
                </div>
                <x-input-error for="menuContent" class="mt-2" />
                @error('menuContent')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <x-button>@lang('app.save')</x-button>
            </div>
        </div>
    </form>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.js"></script>
</div>
