<div>
    <button wire:click="toggleLike" class="flex items-center space-x-1 text-sm text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400">
        <svg class="w-5 h-5 {{ $liked ? 'text-red-600 dark:text-red-400' : 'text-gray-400 dark:text-gray-500' }}" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09 C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5 c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
        </svg>
        <span>{{ $forum->likes_count }}</span>
    </button>
</div>
