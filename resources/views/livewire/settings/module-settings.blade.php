<div class="p-4">
    <!-- Tabs -->
    <div class="mb-4">
        <!-- Container with dark mode background -->
        <div class="bg-white dark:bg-gray-800 rounded-lg">
            <nav class="flex" aria-label="Tabs">
                @foreach($roles as $role)
                    <button
                        wire:click="changeTab('{{ $role }}')"
                        x-data
                        x-init="
                            if (window.location.hash === '#{{ strtolower($role) }}') {
                                $wire.changeTab('{{ $role }}')
                            }
                        "
                        @class([
                            "flex-1 px-4 py-3 text-sm font-medium border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 transition-all duration-300",
                            'border-transparent transform hover:scale-105' => ($activeTab != $role),
                            'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base transform scale-105 shadow-lg' => ($activeTab == $role)
                        ])>
                        <span class="flex items-center justify-center gap-2">
                            {{ $role }}
                        </span>
                    </button>
                @endforeach
            </nav>
        </div>
    </div>

    <div
        x-data="{ shown: false }"
        x-show="shown"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        x-init="shown = true"
        class="mt-6 bg-white dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700"
        wire:loading.class="opacity-50"
        wire:target="changeTab"
    >
        @if(isset($moduleSettings[$activeTab]))
            <div class="mb-4">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-skin-base" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $activeTab }} @lang('modules.settings.moduleSettings')
                    </h2>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 ml-7">
                    @lang('modules.settings.manageAccessPermissions', ['role' => $activeTab])
                </p>
            </div>

            <div class="grid grid-cols-4 gap-4">
                @foreach($moduleSettings[$activeTab] as $setting)
                    <!-- Each module "card" -->
                    <div
                        class="flex flex-col space-y-2 p-4 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 transform transition-all duration-300 hover:scale-105 hover:shadow-lg"
                        wire:key="module-{{ $setting['id'] }}"
                    >
                        <!-- Module Name -->
                        <span class="text-gray-900 dark:text-gray-100 font-medium">
                            {{ $setting['module_name'] }}
                        </span>

                        <div class="flex items-center justify-between">
                            <span
                                class="text-sm transition-colors duration-300"
                                :class="{
                                    'text-green-600 dark:text-green-400': @js($setting['status'] === 'active'),
                                    'text-gray-600 dark:text-gray-400': @js($setting['status'] !== 'active')
                                }"
                            >
                                {{ $setting['status'] === 'active' ? 'Enabled' : 'Disabled' }}
                            </span>

                            <!-- Toggle Button -->
                            <label for="module_{{ $setting['id'] }}" class="relative flex items-center cursor-pointer">
                                <input
                                    type="checkbox"
                                    id="module_{{ $setting['id'] }}"
                                    wire:model.live="moduleSettings.{{ $activeTab }}.{{ $loop->index }}.status"
                                    wire:change="toggleStatus({{ $setting['id'] }})"
                                    @checked($setting['status'] === 'active')
                                    class="sr-only">
                                <span class="h-6 w-11 bg-gray-200 border border-gray-200
                                            rounded-full toggle-bg transition-colors duration-300
                                            dark:bg-gray-600 dark:border-gray-600
                                            peer-checked:bg-skin-base">
                                </span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="mt-4 text-gray-600 dark:text-gray-400">
                    No modules found for {{ $activeTab }} role
                </p>
            </div>
        @endif
    </div>
</div>
