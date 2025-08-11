<div>
    @assets
    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js" defer></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">
    @endassets
    <form wire:submit="updateAssetIssue">
        @csrf
        <div class="space-y-4">

            <div>
                <x-label for="title" value="{{ __('modules.assets.issueTitle') }}" required />
                <x-input id="title" class="block w-full mt-1" type="text" wire:model='title' autocomplete="off" placeholder="{{ __('modules.assets.enterIssueTitle') }}" />
                <x-input-error for="title" class="mt-2" />
            </div>

            {{-- Issue Status --}}
            <div>
                <x-label for="status" value="{{ __('modules.assets.issueStatus') }}" required />
                <x-select id="status" class="block w-full mt-1" wire:model="status">
                    <option value="">{{ __('modules.assets.selectIssueStatus') }}</option>
                    <option value="pending">{{ __('modules.assets.pending') }}</option>
                    <option value="process">{{ __('modules.assets.process') }}</option>
                    <option value="resolved">{{ __('modules.assets.resolved') }}</option>
                </x-select>
                <x-input-error for="status" class="mt-2" />
            </div>

            <div>
                <x-label for="priority" value="{{ __('modules.assets.priority') }}" required />
                <x-select id="priority" class="block w-full mt-1" wire:model="priority">
                    <option value="">{{ __('modules.assets.selectPriority') }}</option>
                    <option value="low">{{ __('modules.assets.low') }}</option>
                    <option value="medium">{{ __('modules.assets.medium') }}</option>
                    <option value="high">{{ __('modules.assets.high') }}</option>
                </x-select>
                <x-input-error for="priority" class="mt-2" />
            </div>


            {{-- Issue Description --}}
            <div>
                <x-label for="description" value="{{ __('modules.assets.issueDescription') }}"/>
                <x-textarea id="description" class="block w-full mt-1" wire:model='description' />
                <x-input-error for="description" class="mt-2" />
            </div>


            {{-- Save Button --}}
            <div class="flex justify-end w-full pb-4 space-x-4 mt-9">
                <x-button>@lang('app.save')</x-button>
            </div>

        </div>
    </form>
</div>