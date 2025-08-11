<tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
    <td class="p-8 dark:text-gray-400 text-center" colspan="{{ $colspan ?? 12 }}">
        <div class="flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-800 rounded-lg p-6 shadow-sm">
            <div class="bg-gray-100 dark:bg-gray-700 rounded-full p-3 mb-4">
                @if(isset($icon))
                    {!! $icon !!}
                @else
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @endif
            </div>
            <h3 class="text-gray-700 dark:text-gray-300 text-lg font-medium mb-1">
                {{ $message ?? __('messages.noResults') }}
            </h3>

            @if(isset($action))
                <button class="mt-4 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ $action }}
                </button>
            @endif
        </div>
    </td>
</tr>
