<div class="px-4 pt-2 xl:grid-cols-3 xl:gap-4 bg-white dark:bg-gray-900">
    @if ($showFilters)
        @include('notices.filters')
    @endif
    <div class="flex items-center justify-end">
        @if($showActions)
            <x-dropdown dropdownClasses="z-50">
                <x-slot name="trigger">
                    <span class="inline-flex">
                        <button type="button"
                            class="inline-flex items-center justify-center p-2 text-sm font-medium text-gray-500 transition duration-150 ease-in-out bg-white border border-gray-300 hover:text-gray-700 focus:outline-none hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 mb-2">
                            @lang('app.action')
                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 8l4 4 4-4" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>
                    </span>
                </x-slot>
                <x-slot name="content">
                    <x-dropdown-link href="javascript:;" wire:click="showSelectedDeleteNotice">
                        <span class="inline-flex items-center justify-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            @lang('app.delete')
                        </span>
                    </x-dropdown-link>
                </x-slot>
            </x-dropdown>
        @endif
    </div>
    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                @if(user_can('Delete Notice Board'))
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    </th>
                                @endif
                                @if (user_can('Show Notice Board'))
                                    <th scope="col"
                                        class="p-4 w-[32rem] text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        @lang('modules.notice.title')
                                    </th>
                                    <th scope="col"
                                        class="p-4 w-[32rem] text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        @lang('modules.notice.roles')
                                    </th>
                                @else
                                    <th scope="col"
                                        class="p-4 w-128 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        @lang('modules.notice.title')
                                    </th>
                                @endif
                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.dateTime')
                                </th>
                                @if (user_can('Update Notice Board') || user_can('Delete Notice Board'))
                                    <th scope="col"
                                        class="p-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">
                                        @lang('app.action')
                                    </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='notice-list-{{ microtime() }}'>
                            @forelse ($notices as $noticeboard)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='notice-{{ $noticeboard->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                @if(user_can('Delete Notice Board'))
                                    <td class="p-4">
                                        <input type="checkbox" wire:model.live="selected" value="{{ $noticeboard->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    </td>
                                @endif
                                @if (user_can('Show Notice Board'))
                                    <td class="p-4 w-[32rem] text-base font-semibold text-gray-900 break-words whitespace-normal dark:text-white">
                                        <a href="javascript:;"
                                            wire:click="showNoticeDetail({{ $noticeboard->id }})"
                                            class="text-base font-semibold hover:underline dark:text-black-400">
                                            {{ $noticeboard->title }}
                                        </a>
                                    </td>

                                    <td class="p-4 w-[32rem] text-base font-medium text-gray-900 break-words whitespace-normal dark:text-white">
                                        @foreach($noticeboard->roles as $role)
                                            <span class="mr-2">
                                                <x-badge type="secondary">{{ $role->display_name }}</x-badge>
                                            </span>
                                        @endforeach
                                    </td>
                                @else
                                    <td class="p-4 w-128 text-base font-semibold text-gray-900 break-words whitespace-normal dark:text-white">
                                        <a href="javascript:;"
                                            wire:click="showNoticeDetail({{ $noticeboard->id }})"
                                            class="text-base font-semibold hover:underline dark:text-black-400">
                                            {{ $noticeboard->title }}
                                        </a>
                                    </td>
                                @endif

                                <td class="p-4 text-base text-xs font-medium text-gray-500 whitespace-nowrap dark:text-white">
                                    {{ $noticeboard->created_at->timezone(timezone())->translatedFormat('d M Y, h:i A') }}
                                </td>
                                @if (user_can('Update Notice Board') || user_can('Delete Notice Board'))
                                    <td class="p-4 space-x-2 whitespace-nowrap text-right">
                                        @if(user_can('Update Notice Board'))
                                            <x-secondary-button wire:click="showEditNotice({{ $noticeboard->id }})" wire:key="notice-edit-{{ $noticeboard->id . microtime() }}">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                                    </path>
                                                    <path fill-rule="evenodd"
                                                        d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                @lang('app.update')
                                            </x-secondary-button>
                                        @endif

                                        @if(user_can('Delete Notice Board'))
                                            <x-danger-button  wire:click="showDeleteNotice({{ $noticeboard->id }})"  wire:key='notice-del-{{ $noticeboard->id . microtime() }}'>
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </x-danger-button>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                            @empty
                                <x-no-results :message="__('messages.noNoticeFound')" />
                            @endforelse

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <div wire:key='notice-table-paginate-{{ microtime() }}'
        class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class=" mb-4 sm:mb-0 w-full">
            {{ $notices->links() }}
        </div>
    </div>

    <x-right-modal wire:model.live="showEditNoticeModal">
        <x-slot name="title">
            {{ __("modules.notice.editNotice") }}
        </x-slot>

        <x-slot name="content">
            @if ($notice)
            @livewire('forms.editNotice', ['notice' => $notice], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showEditNoticeModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-right-modal wire:model.live="showNoticeDetailModal">
        <x-slot name="title">
            {{ __("modules.notice.viewNotice") }}
        </x-slot>

        <x-slot name="content">
            @if ($notice)
            @livewire('forms.showNotice', ['notice' => $notice], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showNoticeDetailModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-confirmation-modal wire:model="confirmDeleteNoticeModal">
        <x-slot name="title">
            @lang('modules.notice.deleteNotice')?
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteNoticeModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            @if ($notice)
            <x-danger-button class="ml-3" wire:click='deleteNotice({{ $notice->id }})' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
            @endif
         </x-slot>
    </x-confirmation-modal>

    <x-confirmation-modal wire:model="confirmSelectedDeleteNoticeModal">
        <x-slot name="title">
            @lang('modules.notice.deleteNotice')?
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmSelectedDeleteNoticeModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click='deleteSelected' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
         </x-slot>
    </x-confirmation-modal>
</div>
