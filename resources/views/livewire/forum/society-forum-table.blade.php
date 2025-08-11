
<div class="space-y-3">
    @forelse ($forums as $item)
        <div class="group">  
            <div class="relative p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 
                        rounded-xl hover:border-orange-400 dark:hover:border-orange-500 transition mb-3">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <img src="{{ $item->user->profile_photo_url }}" alt="{{ $item->user->name }}" title="{{ $item->user->name }}"
                                class="w-10 h-10 object-cover rounded-full">
                    </div>

                    <div class="flex-1 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                        <div>
                            <!-- Title -->
                            <div class="flex items-center justify-between gap-3">
                                <a href="{{ route('society-forum.show', $item->id) }}"
                                    class="block relative overflow-hidden transition-all duration-300 ease-in-out rounded-xl">
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center space-x-2">
                                        <span class="truncate hover:underline">{{ $item->title }}</span>
                                        @if($item->category?->image)
                                            <span class="shrink-0">{!! $item->category->image !!}</span>
                                        @endif
                                    </h3>
                                </a>

                                <!-- Like Button -->
                                <div class="shrink-0">
                                    <livewire:forum.like-forum :forum="$item" :wire:key="'like-'.$item->id" />
                                </div>
                            </div>

                            <!-- Meta Info -->
                            <div class="mt-1 text-xs text-gray-400 dark:text-gray-500 flex flex-wrap items-center gap-1 break-words">
                                <span class="uppercase tracking-wide truncate">{{ $item->user->name }}</span>
                                <span>&bull;</span>
                                <svg class="w-3 h-3 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-12a.75.75 0 00-1.5 0v4c0 .414.336.75.75.75h3a.75.75 0 000-1.5h-2.25V6z" clip-rule="evenodd"/>
                                </svg>
                                <span class="truncate">{{ $item->created_at->format('M d, Y') }}</span>
                                @if($item->replies_count > 0)
                                    <span class="flex items-center space-x-1 ml-2">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M18 10c0 3.866-3.582 7-8 7a8.96 8.96 0 01-4.39-1.117L2 17l1.22-3.658A6.966 6.966 0 012 10c0-3.866 3.582-7 8-7s8 3.134 8 7z" />
                                        </svg>
                                        <span>{{ $item->replies_count }}</span>
                                    </span>
                                @endif
                            </div>

                            <!-- Description -->
                            @if($item->description)
                                @php
                                    $cleaned = html_entity_decode(strip_tags($item->description));
                                    $limitedDescription = Str::limit($cleaned, 100);
                                @endphp
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 break-words">
                                    {!! nl2br(e($limitedDescription)) !!}
                                </p>
                            @endif
                        </div>
                        <div class="shrink-0 sm:ml-4">
                            <x-dropdown>
                                <x-slot name="trigger">
                                    <span class="inline-flex">
                                        <button type="button"
                                            class="inline-flex items-center justify-center p-2 text-sm font-medium text-gray-500 dark:text-gray-200 transition duration-150 ease-in-out rounded-md hover:text-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 4 15">
                                                <path d="M3.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 6.041a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 5.959a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                                            </svg>
                                        </button>
                                    </span>
                                </x-slot>
                                <x-slot name="content">
                                    @if($item->created_by == user()->id || user_can('Update Forum'))
                                        <x-dropdown-link href="javascript:;" wire:click="showEditForum({{ $item->id }})">
                                            <span class="inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                                    </path>
                                                    <path fill-rule="evenodd"
                                                        d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                @lang('app.edit')
                                            </span>
                                        </x-dropdown-link>
                                    @endif
                                    <x-dropdown-link href="{{ route('society-forum.show', $item->id) }}">
                                        <span class="inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            @lang('app.view')
                                        </span>
                                    </x-dropdown-link>
                                    @if($item->created_by == user()->id || user_can('Delete Forum'))
                                        <x-dropdown-link href="javascript:;" wire:click="showDeleteForum({{ $item->id }})">
                                            <span class="inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                @lang('app.delete')
                                            </span>
                                        </x-dropdown-link>
                                    @endif
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            @lang('messages.noForumFound')
        </div>
    @endforelse
    
    <div wire:key='forum-table-paginate-{{ microtime() }}'
        class="sticky bottom-0 right-0 items-center w-full p-4 border-gray-200 sm:flex sm:justify-between dark:border-gray-700">
        <div class="flex items-center mb-4 sm:mb-0 w-full">
            {{ $forums->links() }}
        </div>
    </div>

    <x-right-modal wire:model.live="showEditForumModal">
        <x-slot name="title">
            {{ __("modules.forum.editForum") }}
        </x-slot>

        <x-slot name="content">
            @if ($editForum)
            @livewire('forms.editForum', ['forum' => $editForum], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showEditForumModal', false)" wire:loading.attr="disabled">
                {{ __('app.close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-confirmation-modal wire:model="confirmDeleteForumModal">
        <x-slot name="title">
            @lang('modules.forum.deleteForum')?
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteForumModal')" wire:loading.attr="disabled">
                {{ __('app.cancel') }}
            </x-secondary-button>

            @if ($deleteForum)
            <x-danger-button class="ml-3" wire:click='deleteSocietyForum({{ $deleteForum->id }})' wire:loading.attr="disabled">
                {{ __('app.delete') }}
            </x-danger-button>
            @endif
         </x-slot>
    </x-confirmation-modal>

</div>
             
