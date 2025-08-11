<div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mt-10">
    @if (is_null($parentReplyId))
        <form wire:submit.prevent="submit" class="mb-6">
            @csrf
            <x-label for="reply" value="{{ __('Write a Reply') }}"/>
            <x-textarea id="reply" class="block w-full mt-1" wire:model='reply' :placeholder="__('Share your thoughts...')" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg shadow-sm"/>
            <x-input-error for="reply" class="mt-2" />

            <x-button type="submit" class="mt-4 ">
                @lang('app.reply')
            </x-button>
        </form>
    @endif
    @if ($replies->isNotEmpty())
        <div class="mb-4">
            <button wire:click="toggleReplies" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                {{ $showReplies ? __('Hide Replies') : __('Show All Replies') }}
            </button>
        </div>
    @endif

    @if ($showReplies)
        <div class="space-y-4">
            @foreach ($replies as $reply)
                @include('society-forum.reply', ['reply' => $reply])
            @endforeach
        </div>
    @endif
</div>