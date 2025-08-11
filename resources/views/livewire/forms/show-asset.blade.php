<div>

    <div class="grid grid-cols-2 mb-6">

        <div>
            <x-label value="{{ __('modules.assets.name') }}" />
            <p class="mt-2 text-gray-600 dark:text-gray-200">{{$name}}</p>
        </div>

        <div>
            <x-label value="{{ __('modules.assets.category') }}" />
            <p class="mt-2 text-gray-600 dark:text-gray-200">{{$category}}</p>
        </div>


    </div>

    <div class="grid grid-cols-2 mb-6">

        <div>
            <x-label value="{{ __('modules.assets.condition') }}" />
            <p class="mt-2 text-gray-600 dark:text-gray-200">{{$condition}}</p>
        </div>




     </div>

    <div class="grid grid-cols-2 mb-6">
       
        <div>
            <x-label class="mb-2" value="{{ __('modules.assets.purchaseDate') }}" />
            <p class="mt-2 text-gray-600 dark:text-gray-200">{{ \Carbon\Carbon::parse($purchaseDate)->format('d F Y') }}</p>

        </div>


    </div>

    <div class="grid grid-cols-2 mb-6">


        @if($documentPath)
            <div class="relative w-full group">
                <x-label class="mb-2" value="{{ __('modules.assets.document') }}" />
                <div class="relative w-64 h-56 p-1 overflow-hidden rounded bg-gray-50 ring-gray-300 ring-1 dark:ring-gray-500">
                    @if(Str::endsWith($documentPath, '.pdf'))
                    <a href="{{ $documentPath }}" target="_blank">
                        <div class="flex items-center justify-center w-full h-full bg-gray-200">
                            <svg class="w-12 h-12 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14.5 2h-5A2.5 2.5 0 007 4.5v15A2.5 2.5 0 009.5 22h5a2.5 2.5 0 002.5-2.5v-15A2.5 2.5 0 0014.5 2zm-5 1h5a1.5 1.5 0 011.5 1.5v15a1.5 1.5 0 01-1.5 1.5h-5A1.5 1.5 0 018 18.5v-15A1.5 1.5 0 019.5 3zm3.5 8h-3v1h1.5v3H10v1h3v-5zM13.5 5h-3v1h1.5v2H10v1h3.5V5z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">View PDF</p>
                        </div>
                    </a>
                    @else
                        <a href="{{ $documentPath }}" target="_blank">
                            <img class="object-cover w-full h-full" src="{{ $documentPath }}" alt="Payment Proof" />
                        </a>
                    @endif
                    <div class="absolute inset-0 flex items-end justify-end p-2 transition-opacity duration-300 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100">
                        <a href="{{ $documentPath }}" target="_blank" class="px-4 py-2 mr-2 text-white rounded shadow hover:bg-gray-800">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 4.5C6.75 4.5 3 12 3 12s3.75 7.5 9 7.5 9-7.5 9-7.5-3.75-7.5-9-7.5zM12 16.5c-2.25 0-4.5-1.5-4.5-4.5s2.25-4.5 4.5-4.5 4.5 1.5 4.5 4.5-2.25 4.5-4.5 4.5zM12 10.5a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" />
                            </svg>
                        </a>
                        <a href="{{ $documentPath }}" download target="_blank" wire:loading.attr="disabled" class="px-4 py-2 text-white rounded shadow hover:bg-gray-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                <path d="M12 16l4-4h-3V4h-2v8H8l4 4zM5 20h14v-2H5v2z" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @endif

    </div>

    <div class="flex w-full pb-4 mt-6 space-x-4">
        <x-button-cancel  wire:click="$dispatch('hideAssetDetail')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
    </div>
</div>

