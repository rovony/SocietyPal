<div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg shadow-lg p-8 border dark:border-gray-700 w-full">
    <div class="space-y-4">

        <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
            <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.assets.assetName') }}</strong>
            <span>{{ $assetName }}</span>
        </p>

        <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
            <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.assets.issueTitle') }}</strong>
            <span>{{ $title }}</span>
        </p>

        <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
            <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.assets.issueStatus') }}</strong>
            <span class="capitalize">{{ $status }}</span>
        </p>

        <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
            <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.assets.priority') }}</strong>
            <span class="capitalize">{{ $priority }}</span>
        </p>

        @if($description)
            <p class="border-b pb-2 border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400 block mb-1">{{ __('modules.assets.issueDescription') }}</strong>
                <span class="block text-gray-700 dark:text-gray-300">{{ $description }}</span>
            </p>
        @endif

        @if($documentPath)
            <div class="group">
                <x-label class="mb-2" value="{{ __('modules.assets.addImage') }}" />
                <div class="group relative w-24 h-24 p-1 overflow-hidden rounded bg-gray-50 ring-gray-300 ring-1 dark:ring-gray-500">
                    @if(Str::endsWith($documentPath, '.pdf'))
                        <a href="{{ $documentPath }}" target="_blank" class="flex flex-col items-center justify-center w-full h-full bg-gray-200">
                            <svg class="w-12 h-12 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14.5 2h-5A2.5 2.5 0 007 4.5v15A2.5 2.5 0 009.5 22h5a2.5 2.5 0 002.5-2.5v-15A2.5 2.5 0 0014.5 2zm-5 1h5a1.5 1.5 0 011.5 1.5v15a1.5 1.5 0 01-1.5 1.5h-5A1.5 1.5 0 018 18.5v-15A1.5 1.5 0 019.5 3zm3.5 8h-3v1h1.5v3H10v1h3v-5zM13.5 5h-3v1h1.5v2H10v1h3.5V5z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">View PDF</p>
                        </a>
                    @else
                        <a href="{{ $documentPath }}" target="_blank">
                            <img class="object-cover w-full h-full" src="{{ $documentPath }}" alt="Issue Document" />
                        </a>
                    @endif

                    <div class="absolute inset-0 flex justify-end items-end p-3 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition z-10">
                        <a href="{{ $documentPath }}" target="_blank" 
                        class="bg-white dark:bg-gray-700 p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 shadow">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 4.5C6.75 4.5 3 12 3 12s3.75 7.5 9 7.5 9-7.5 9-7.5-3.75-7.5-9-7.5zM12 16.5c-2.25 0-4.5-1.5-4.5-4.5s2.25-4.5 4.5-4.5 4.5 1.5 4.5 4.5-2.25 4.5-4.5 4.5zM12 10.5a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" />
                            </svg>                  
                        </a>
                        <a href="{{ $documentPath }}" download 
                        class="ml-2 bg-white dark:bg-gray-700 p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 shadow">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                <path d="M12 16l4-4h-3V4h-2v8H8l4 4zM5 20h14v-2H5v2z" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>