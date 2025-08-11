<div>
    <form wire:submit.prevent="submitForm">
        @csrf
        <div class="space-y-4">

            <div>
                <x-label for="title" :value="__('modules.notice.title')" required/>
                <x-input id="title" class="block mt-1 w-full" type="text" autofocus wire:model='title' autocomplete="off" placeholder="{{ __('placeholders.noticeTitlePlaceholder') }}" />
                <x-input-error for="title" class="mt-2" />
            </div>

            <div>
                <x-label for="description" :value="__('modules.notice.description')" required/>
                <x-textarea class="block mt-1 w-full" :placeholder="__('placeholders.noticeDescriptionPlaceholder')"
                    wire:model='description' rows='3' />
                <x-input-error for="description" class="mt-2" />
            </div>

            <div>
                <x-label for="selectedRoles" :value="__('modules.user.role')" required/>
                <div class="mt-1">
                    <div class="grid grid-cols-2 gap-4">
                        @foreach ($roles as $role)
                            <label class="flex items-center">
                                <input type="checkbox" wire:model='selectedRoles' value="{{ $role->id }}" class="mr-2"
                                       {{ in_array($role->id, $selectedRoles) ? 'checked' : '' }} />
                                <span>{{ $role->display_name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <x-input-error for="selectedRoles" class="mt-2" />
            </div>
        </div>

        <div class="flex w-full pb-4 space-x-4 mt-6">
            <x-button>@lang('app.update')</x-button>
            <x-button-cancel wire:click="$dispatch('hideEditNotice')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>
