<div>
    @if($errorMessage)
    <x-alert type="danger">{{$errorMessage}}</x-alert>
    @endif

    <form wire:submit="awsTest">
        @csrf
        <div class="grid grid-cols-1">
               <x-label for="file" :value="__('modules.storageSetting.uploadFile')" required/>
               <input id="file" type="file" class="block mt-1 w-full" wire:model="file" />
               <x-input-error for="file" class="mt-2" />
        </div>
        <div class="flex justify-end w-full pb-4 space-x-4 mt-6">
            <x-button class="ml-3">@lang('app.save')</x-button>

            @if ($showFileLink) 
                <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-black text-white rounded hover:bg-gray-800">
                    @lang('app.viewFile')
                </a>
            @endif
        </div>
    
    </form>
</div>
