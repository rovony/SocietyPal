<div class="p-4 mt-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">

    <form wire:submit.prevent="saveHeader">
         <!-- Language Enable Radio Buttons -->
                <div class="mb-6">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">
                        @lang('modules.settings.selectLanguage')
                    </h3>
                    <div class="flex flex-wrap gap-4">
                       @foreach($languageEnable as $value => $label)
                        <label class="relative flex items-center cursor-pointer group">
                            <input type="radio"
                                wire:model.live="languageSettingid"
                                value="{{ $label->id }}"
                                class="sr-only peer">
                                <span class="px-4 py-2 text-sm transition-colors border border-gray-200 rounded-md dark:border-gray-700 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900 peer-checked:text-indigo-600 dark:peer-checked:text-indigo-400 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    {{ $label->language_name }}
                                </span>
                        </label>
                    @endforeach
                    </div>
                </div>

        <!-- Content Section -->
        <div class="p-4 mt-4 space-y-3 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <!-- Header Title -->
            <div class="sm:col-span-2">
                <label for="headerTitle" class="block text-sm font-medium text-gray-700">
                    @lang('modules.settings.headerTitle')
                </label>
                <input type="text"
                       id="headerTitle"
                       wire:model="headerTitle"
                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <x-input-error for="headerTitle" class="mt-2" />

            </div>

            <!-- Header Description -->
            <div class="sm:col-span-2">
                <label for="headerDescription" class="block text-sm font-medium text-gray-700">
                    @lang('modules.settings.headerDescription')
                </label>
                <textarea id="headerDescription"
                          wire:model="headerDescription"
                          rows="3"
                          class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            <x-input-error for="headerDescription" class="mt-2" />

            </div>
            <!-- Select Image -->
            <div class="sm:col-span-2">
                <label for="headerImage" class="block text-sm font-medium text-gray-700">
                        @lang('modules.settings.headerImage')
                    </label>
                <input
                    class="block w-full mt-1 text-sm border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 text-slate-500"
                    type="file" wire:model="headerImage">
                <x-input-error for="headerImage" class="mt-2" />
            </div>
        @if ($existingImageUrl)
                <div class="mt-4">
                    <label for="headerImage" class="block text-sm font-medium text-gray-700">
                    @lang('modules.settings.preview')
                 </label>
                    <div class="relative mt-2">
                        @if (Str::endsWith($existingImageUrl, ['.jpg', '.jpeg', '.png']))
                            <div class="relative inline-block">
                                <img src="{{ $existingImageUrl }}" alt="Expense Receipt"
                                    class="w-32 h-auto border rounded-lg shadow-md">
                                <button type="button" wire:click="removeImage"
                                    class="absolute p-1 text-white bg-gray-500 rounded-full -top-2 -right-2 hover:bg-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <p class="mt-4 text-gray-500">@lang('modules.settings.noReceiptAvailable')</p>
            @endif
        <!-- Save Button -->
            <x-button class="mt-4">@lang('app.update')</x-button>
        </div>

    </form>
</div>
