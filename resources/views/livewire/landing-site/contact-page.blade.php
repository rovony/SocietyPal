<div class="p-4 mt-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="space-y-8">
        <section class="p-3 bg-white rounded-lg shadow-sm dark:bg-gray-800">
            <form wire:submit="saveContactHeading">
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
                <div class="mb-4">
                    <label for="contactHeading" class="block text-sm font-medium text-gray-700">
                        @lang('modules.settings.title')
                    </label>
                    <input type="text"
                        id="contactHeading"
                        wire:model="contactHeading"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <x-input-error for="contactHeading" class="mt-2" />
                </div>
                <x-button class="mt-4">@lang('app.update')</x-button>
            </form>
        </section>

        <section class="p-3 bg-white rounded-lg shadow-sm dark:bg-gray-800">
            <div class="space-y-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    @lang('modules.settings.addContact')
                </h2>
                <form wire:submit="saveContact" class="mt-4">
                    <div class="mb-4">
                        <x-label for="email" value="{{ __('modules.settings.email') }}" />
                        <input type="email"
                            id="email"
                            wire:model="email"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <x-input-error for="email" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-label for="contactCompany" value="{{ __('modules.settings.contactCompany') }}" />
                        <input type="text"
                            id="contactCompany"
                            wire:model="contactCompany"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <x-input-error for="contactCompany" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-label for="address" value="{{ __('modules.settings.address') }}" />
                        <textarea
                            id="address"
                            wire:model="address"
                            rows="3"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </textarea>
                        <x-input-error for="address" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-label for="contactImage" value="{{ __('modules.settings.contactImage') }}" />
                        <input
                            type="file"
                            id="contactImage"
                            wire:model="contactImage"
                            accept="image/*"
                            class="block w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                        >
                        <x-input-error for="contactImage" class="mt-2" />

                       @if ($existingImageUrl)
                            <div class="mt-4">
                                <label for="headerDescription" class="block text-sm font-medium text-gray-700">
                                @lang('modules.settings.preview')
                            </label>
                                <div class="mt-2">
                                    @if (Str::endsWith($existingImageUrl, ['.jpg', '.jpeg', '.png', '.gif']))
                                        <img src="{{ $existingImageUrl }}" alt="Expense Receipt"
                                            class="w-32 h-auto border rounded-lg shadow-md">
                                    @endif
                                    @if (Str::endsWith($existingImageUrl, ['.pdf']))
                                        <img src="{{ asset('/img/receipt icon.jpg') }}" alt="Header Image"
                                            class="w-32 h-auto border rounded-lg shadow-md">
                                    @endif
                                </div>
                            </div>
                            @else
                                <p class="mt-4 text-gray-500">@lang('modules.settings.noReceiptAvailable')</p>
                        @endif
                    </div>
                    <div class="flex justify-start w-full pb-4 mt-6 space-x-4">
                        <x-button>@lang('app.update')</x-button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
