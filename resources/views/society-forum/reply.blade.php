<div class="flex items-start gap-3 mt-6">
    <img class="w-10 h-10 rounded-full object-cover" src="{{ $reply->user->profile_photo_url }}" alt="{{ $reply->user->name }}">

    <div class="flex flex-col w-full max-w-3xl p-4 bg-gray-100 border border-gray-200 rounded-e-xl rounded-es-xl dark:bg-gray-700 dark:border-gray-600">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $reply->user->name }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-300">{{ $reply->created_at->diffForHumans() }}</span>
            </div>

            <div class="relative">
                <button id="dropdownToggle{{ $reply->id }}" type="button"
                    class="p-2 text-gray-500 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600"
                    onclick="document.getElementById('dropdownMenu{{ $reply->id }}').classList.toggle('hidden')">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 4 15">
                        <path d="M3.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 6.041a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 5.959a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                    </svg>
                </button>
                <div id="dropdownMenu{{ $reply->id }}" class="absolute right-0 z-10 mt-2 w-40 bg-white rounded-md shadow-md border dark:bg-gray-800 dark:border-gray-600 hidden">
                    <ul class="text-sm text-gray-700 dark:text-gray-200">
                        <li>
                            <button wire:click.prevent="setReplyingTo({{ $reply->id }})" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">
                                @lang('app.reply')
                            </button>
                        </li>
                        @if($reply->user_id == user()->id)
                            <li>
                                <button wire:click.prevent="deleteReply({{ $reply->id }})" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    @lang('app.delete')
                                </button>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <p class="text-sm text-gray-800 dark:text-gray-100">{{ $reply->reply }}</p>
    </div>
</div>

@if ($parentReplyId === $reply->id)
    <div class="ml-14 mt-4">
        <form wire:submit.prevent="submit" class="space-y-3">
            <x-textarea wire:model="reply" placeholder="Write a reply..."
                class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg shadow-sm" />
            <x-input-error for="reply" class="text-sm text-red-500" />
            <div class="flex gap-2">
                <x-button type="submit" class="text-sm">@lang('app.reply')</x-button>
                <x-button-cancel wire:click="cancelReply" wire:loading.attr="disabled" class="text-sm">
                    @lang('app.cancel')
                </x-button-cancel>
            </div>
        </form>
    </div>
@endif

@foreach ($reply->children as $child)
    <div class="ml-14">
        @include('society-forum.reply', ['reply' => $child])
    </div>
@endforeach
