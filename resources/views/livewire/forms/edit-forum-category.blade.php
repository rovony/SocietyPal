<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">

            <div>
                <x-label for="name" value="{{ __('modules.settings.categoryName') }}" required="true" />
                <x-input id="name" class="block w-full mt-1" type="text" autofocus wire:model='name' />
                <x-input-error for="name" class="mt-2" />
            </div>

            <div class="sm:col-span-2 mt-4">
                <x-label for="selectedIcon" value="{{ __('app.icon') }}" required="true" />
                <div class="relative" x-data="{ open: false }">
                    <div
                        class="border rounded w-full px-3 py-2 flex items-center cursor-pointer bg-white"
                        @click="open = !open"
                    >
                        @if ($selectedIcon)
                            <x-dynamic-component :component="'heroicon-o-' . $selectedIcon" class="w-5 h-5 text-gray-600 mr-2" />
                            <span class="text-gray-700">{{ $selectedIcon }}</span>
                        @else
                            <span class="text-gray-400">@lang('app.selectIcon')</span>
                        @endif
                        <svg class="w-4 h-4 text-gray-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

                    <div
                        x-show="open"
                        @click.outside="open = false"
                        class="absolute z-50 mt-1 bg-white border border-gray-300 rounded shadow-lg w-full max-h-60 overflow-y-auto"
                        x-cloak
                    >
                        <div class="p-2">
                            <input
                                type="text"
                                wire:model.debounce.300ms="search"
                                placeholder="@lang('app.searchIcon')"
                                class="w-full px-2 py-1 border border-gray-300 rounded mb-2"
                            >
                        </div>
                        <div class="grid grid-cols-6 gap-2 p-2">
                            @foreach ($icons as $icon)
                                @if ($search === '' || Str::contains($icon, strtolower($search)))
                                    <button
                                        wire:click.prevent="selectIcon('{{ $icon }}')"
                                        class="p-2 border rounded hover:bg-gray-100 flex items-center justify-center {{ $selectedIcon === $icon ? 'border-blue-500 bg-blue-100' : '' }}"
                                        title="{{ $icon }}"
                                        type="button"
                                    >
                                        <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-5 h-5" />
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <x-input-error for="selectedIcon" class="mt-2" />
            </div>

        </div>

        <div class="flex w-full pb-4 mt-6 space-x-4">
            <x-button>@lang('app.update')</x-button>
            <x-button-cancel  wire:click="$dispatch('hideEditForumCategory')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>
