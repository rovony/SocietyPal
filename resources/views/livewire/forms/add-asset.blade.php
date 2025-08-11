<div>
    <form wire:submit.prevent="submitForm">
        @csrf
        <div class="space-y-4">
            <div>
                <x-label for="name" :value="__('modules.assets.name')" required/>
                <x-input id="name" class="block w-full mt-1" type="text" autofocus wire:model='name'  autocomplete="off" placeholder="{{ __('Enter Name') }}"/>
                <x-input-error for="name" class="mt-2" />
            </div>

            <div class="flex space-x-4">
                <div class="w-1/2">
                    <x-label for="category" :value="__('modules.assets.category')" required/>
                    <x-select id="category" class="block w-full mt-1" wire:model='category'>
                        <option value="">@lang('modules.assets.selectAssetCategory')</option>

                        @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                    </x-select>
                    <x-input-error for="category" class="mt-2" />
                </div>

                <div class="w-1/2">
                    <x-label for="condition" :value="__('modules.assets.condition')" required/>
                    <x-select id="condition" class="block w-full mt-1" wire:model='condition'>
                        <option value="">Select Condition</option>
                        <option value="New">New</option>
                        <option value="Good">Good</option>
                        <option value="Needs Repair">Needs Repair</option>
                    </x-select>
                    <x-input-error for="condition" class="mt-2" />
                </div>
            </div>


            <div class="flex space-x-4">
                <div class="w-1/2">
                    <x-label for="tower" :value="__('modules.settings.selectTower')" required="true" />
                        <x-select id="tower" class="block w-full mt-1" wire:model.live="selectedTower">
                            <option value="">@lang('modules.settings.societyTower')</option>
                            @foreach ($towers as $tower)
                                <option value="{{ $tower->id }}">{{ $tower->tower_name }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error for="selectedTower" class="mt-2" />
                </div>

                <div class="w-1/2">
                    <x-label for="floor" :value="__('modules.settings.selectFloor')"/>
                    <x-select id="floor" class="block w-full mt-1" wire:model.live="selectedFloor">
                        <option value="">@lang('modules.settings.selectFloor')</option>
                        @foreach ($floors as $floor)
                            <option value="{{ $floor->id }}">{{ $floor->floor_name }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error for="selectedFloor" class="mt-2" />
                </div>

            </div>

            <div>
                <x-label for="apartmentId" :value="__('modules.settings.societyApartmentNumber')"/>
                <x-select id="apartmentId" class="block w-full mt-1" wire:model="apartmentId">
                    <option value="">@lang('modules.utilityBills.selectApartmentNumber')</option>
                    @foreach ($apartments as $item)
                        <option value="{{ $item->id }}">{{ $item->apartment_number}}</option>
                    @endforeach
                </x-select>
                <x-input-error for="apartmentId" class="mt-2" />
            </div>



            <div>
                <x-label for="location" :value="__('modules.assets.location')"/>
                <x-input id="location" class="block w-full mt-1" type="text" wire:model='location'  autocomplete="off" placeholder="Enter location"/>
                <x-input-error for="location" class="mt-2" />
            </div>
            <div>
                <x-label for="purchaseDate" value="{{ __('modules.assets.purchaseDate') }}"/>
                <x-datepicker class="block w-full mt-1" wire:model.live="purchaseDate" id="purchaseDate" autocomplete="off" placeholder="{{ __('modules.utilityBills.chooseBillDate') }}" />
                <x-input-error for="purchaseDate" class="mt-2" />
            </div>

            <div>
                <x-label for="documentPath" value="{{ __('modules.user.uploadDocument') }}"/>
                <input class="block w-full mt-1 text-sm border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 text-slate-500" type="file" wire:model="documentPath">
                <x-input-error for="documentPath" class="mt-2" />
            </div>
        </div>

        <div class="flex w-full pb-4 mt-6 space-x-4">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel wire:click="$dispatch('hideAddAsset')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>