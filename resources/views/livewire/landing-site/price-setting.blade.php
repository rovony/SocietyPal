<div class="p-4 mt-4 bg-white dark:bg-gray-800">
    <div class="space-y-8">
        <section class="p-3 bg-white rounded-lg dark:bg-gray-800">
        <form wire:submit.prevent="priceSettingSave">
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
            <div class="space-y-3 bg-white dark:border-gray-700 dark:bg-gray-800 ">
                <!-- Header Title -->
                <div class="sm:col-span-2">
                    <label for="priceTitle" class="block text-sm font-medium text-gray-700">
                        @lang('modules.settings.priceTitle')
                    </label>
                    <x-input type="text"
                           id="priceTitle" wire:model="priceTitle" class="block w-full mt-1" />
                    <x-input-error for="priceTitle" class="mt-2" />

                </div>

                <!-- Header Description -->
                <div class="sm:col-span-2">
                    <label for="priceDescription" class="block text-sm font-medium text-gray-700">
                        @lang('modules.settings.priceDescription')
                    </label>
                    <x-textarea id="priceDescription"
                              wire:model="priceDescription" rows="3" class="block w-full mt-1">
                    </x-textarea>
                    <x-input-error for="priceDescription" class="mt-2" />

                </div>


                <x-button class="mt-4">@lang('app.update')</x-button>
            </div>

        </form>
        </section>
    </div>
    </div>
