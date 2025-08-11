<div class="flex flex-col mb-12">
    <div class="overflow-x-auto ">

        <div class="mb-4">
            <x-button type='button' wire:click="$set('showAddAssetIssue', true)">@lang('app.add')</x-button>
        </div>
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow">
                <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                #
                            </th>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.assets.issueTitle')
                            </th>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.assets.issueStatus')
                            </th>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.assets.priority')
                            </th>
                            <th scope="col"
                            class="py-2.5 px-4 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                @lang('app.action')
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key="asset-issue-list-{{ microtime() }}">

                        @forelse ($assetIssues as $assetIssue)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key="asset-issue-{{ $assetIssue->id . rand(1111, 9999) . microtime() }}" wire:loading.class.delay="opacity-10">
                            <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $loop->index + 1 }}
                            </td>

                            <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                 <a class="text-base font-semibold hover:underline dark:text-black-400" href="javascript:;"
                                      wire:click="showAssetIssues({{ $assetIssue->id }})">
                                    {{ $assetIssue->title ?? '--' }}
                            </a>
                            </td>
                            <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">

                            @if($assetIssue->status == 'resolved')
                                 <x-badge type="success">{{ucFirst($assetIssue->status)}}</x-badge>
                            @elseif($assetIssue->status == 'pending')
                                <x-badge type="warning">{{ucFirst($assetIssue->status)}}</x-badge>
                            @else
                                <x-badge type="danger">{{ucFirst($assetIssue->status)}}</x-badge>
                            @endif
                            </td>
                            <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                {{  ucfirst($assetIssue->priority) ?? '--' }}
                            </td>
                            <td class="p-4 space-x-2 text-right whitespace-nowrap">
                                <x-danger-button wire:click="showDeleteAsset({{ $assetIssue->id }})" wire:key='asset-del-{{ $assetIssue->id . microtime() }}'>
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </x-danger-button>
                                <x-secondary-button wire:click="showEditAssetIssue({{ $assetIssue->id }})" wire:key="asset-issue-edit-{{ $assetIssue->id . microtime() }}">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path>
                                        <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path>
                                    </svg>
                                </x-secondary-button>
                           </td>
                        </tr>
                        @empty
                            <x-no-results :message="__('messages.noAssetIssueFound')" />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>


    <x-dialog-modal wire:model.live="showAssetIssueModal">
        <x-slot name="title">
            {{ __("modules.assets.editIssue") }}
        </x-slot>
        <x-slot name="content">
            @if ($selectedIssue)
                @livewire('assets.edit-asset-issue', ['assetId' => $assetId, 'assetIssueId' => $this->selectedIssue], key(microtime()))
            @endif
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showAssetIssueModal', false)" wire:loading.attr="disabled">
                {{ __("app.close") }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model.live="showIssueModal">
        <x-slot name="title">
            {{ __("modules.assets.issueDetails") }}
        </x-slot>
        <x-slot name="content">
            @if ($issueId)
                @livewire('assets.show-issues-details', ['assetIssueId' => $this->issueId], key(microtime()))
            @endif
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showIssueModal', false)" wire:loading.attr="disabled">
                {{ __("app.close") }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>


    <x-dialog-modal wire:model.live="showAddAssetIssue">
        <x-slot name="title">
            {{ __("modules.assets.AddIssue") }}
        </x-slot>
        <x-slot name="content">
            @livewire('assets.add-asset-issue', ['assetId' => $assetId], key(str()->random(50)))
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showAddAssetIssue', false)" wire:loading.attr="disabled">
                {{ __("app.close") }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>

    <x-confirmation-modal wire:model="confirmDeleteAssetModal">
        <x-slot name="title">
            @lang('modules.assets.deleteAssetIssue')?
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteAssetModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            @if ($asset)
            <x-danger-button class="ml-3" wire:click='deleteAsset({{ $assetId }})' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
            @endif
         </x-slot>
    </x-confirmation-modal>

    <x-confirmation-modal wire:model="confirmSelectedDeleteAssetModal">
        <x-slot name="title">
            @lang('modules.assets.deleteAssetIssue')?
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmSelectedDeleteAssetModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click='deleteSelected' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
         </x-slot>
    </x-confirmation-modal>

</div>
