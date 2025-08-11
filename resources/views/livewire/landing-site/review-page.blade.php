<div class="p-4 mt-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="space-y-8">
        <section class="p-3 bg-white rounded-lg shadow-sm dark:bg-gray-800">
            <form wire:submit="saveReviewHeading">
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

                <div class="mb-6">
                    <label for="reviewHeading" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        @lang('modules.settings.title')
                    </label>
                    <input type="text"
                        id="reviewHeading"
                        wire:model="reviewHeading"
                        class="w-full border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Enter review heading">
                    <x-input-error for="reviewHeading" class="mt-2" />
                </div>

                <x-button class="w-full sm:w-auto">
                    @lang('app.update')
                </x-button>
            </form>
        </section>

        <section class="p-3 bg-white rounded-lg shadow-sm dark:bg-gray-800">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    @lang('modules.settings.reviews')
                </h2>
                <x-button wire:click="addReviewModal" class="flex items-center">
                    @lang('modules.settings.addReview')
                </x-button>
            </div>

            <div class="space-y-4">
                @forelse($reviewsDetails as $review)
                    <div class="p-4 transition-all border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 hover:shadow-md">
                        <div class="mb-2">
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $review->reviewer_name }}</span>
                            @if($review->reviewer_designation)
                                <span class="text-gray-500 dark:text-gray-400">({{ $review->reviewer_designation }})</span>
                            @endif
                        </div>
                        <p class="mb-4 text-gray-700 dark:text-gray-300">{{ $review->reviews }}</p>
                        <div class="flex flex-wrap gap-2">
                            <x-secondary-button-table
                                wire:click='editReviewPage({{ $review->id }})'
                                wire:key='review-edit-{{ $review->id . microtime() }}'
                                class="text-sm">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"/>
                                    <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                                </svg>
                                @lang('app.update')
                            </x-secondary-button-table>
                            <x-danger-button
                                wire:click="deleteReview({{ $review->id }})"
                                class="text-sm">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"/>
                                </svg>
                                @lang('app.delete')
                            </x-danger-button>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <p class="text-gray-500 dark:text-gray-400">@lang('modules.settings.noReviews')</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <x-dialog-modal wire:model.live="editReviewPageModal">
        <x-slot name="title">
            @lang('modules.settings.editReview')
        </x-slot>

        <x-slot name="content">
            @if($reviewDetail)
                @livewire('landing-site.edit-review-page', ['reviewDetail' => $reviewDetail, 'languageEnable' => $languageEnable], key('edit-review-page-' . microtime()))
            @endif
        </x-slot>
    </x-dialog-modal>

        <x-dialog-modal wire:model.live="showAddReviewModal">
        <x-slot name="title">
            @lang('modules.settings.addReview')
        </x-slot>

        <x-slot name="content">
            <form wire:submit="saveReview">
                <div class="space-y-3">
                    <div class="sm:col-span-2">
                        <div class="mt-4">
                            <label for="reviewerName" class="block text-sm font-medium text-gray-700">
                                @lang('modules.settings.reviewerName')
                            </label>
                            <input type="text"
                                   id="reviewerName"
                                   wire:model="reviewerName"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   placeholder="Enter reviewer name">
                            <x-input-error for="reviewerName" class="mt-2" />
                        </div>
                        <div class="mt-4">
                            <label for="reviewerDesignation" class="block text-sm font-medium text-gray-700">
                                @lang('modules.settings.reviewerDesignation')
                            </label>
                            <input type="text"
                                   id="reviewerDesignation"
                                   wire:model="reviewerDesignation"
                                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   placeholder="Enter designation">
                            <x-input-error for="reviewerDesignation" class="mt-2" />
                        </div>
                        <div class="mt-4">

                        <label for="reviewText" class="block text-sm font-medium text-gray-700">
                            @lang('modules.settings.addReview')
                        </label>
                        <textarea id="reviewText"
                                wire:model="reviewText"
                                rows="3"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                        <x-input-error for="reviewText" class="mt-2" />
                        </div>

                    </div>
                </div>
                <div class="flex justify-end w-full pb-4 mt-6 space-x-4">
                    <x-button>@lang('app.save')</x-button>
                    <x-button-cancel wire:click="$dispatch('hideEditReviewModal')">@lang('app.cancel')</x-button-cancel>
                </div>
            </form>
        </x-slot>

    </x-dialog-modal>
</div>
