<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">
            <div>
                <x-search-dropdown id="apartmentId" label="Apartment Number" model="apartmentId" :options="$apartments->map(fn($a) => ['id' => $a->id, 'number' => $a->apartment_number])" placeholder="Select Apartment Number" :required="true"/>
            </div>

            <div>
                <x-label for="billTypeId" :value="__('modules.settings.billType')" required="true" />
                <x-select id="billTypeId" class="block w-full mt-1" wire:model="billTypeId">
                    <option value="">@lang('modules.utilityBills.selectBillType')</option>
                    @foreach ($billTypes as $item)
                        <option value="{{ $item->id }}">{{ $item->name}}</option>
                    @endforeach
                </x-select>
                <x-input-error for="billTypeId" class="mt-2" />
            </div>

            <div>
                <x-label for="billAmount" value="{{ __('modules.utilityBills.billAmount') }}" required="true" />
                <x-input id="billAmount" class="block w-full mt-1" min='0' step="0.01" type="number" wire:model='billAmount' />
                <x-input-error for="billAmount" class="mt-2" />
            </div>

            <div>
                <x-label for="billDate" value="{{ __('modules.utilityBills.billDate') }}" required="true "/>
                <x-datepicker class="block w-full mt-1" wire:model.live="billDate" id="billDate" autocomplete="off" placeholder="{{ __('modules.utilityBills.chooseBillDate') }}" />
                <x-input-error for="billDate" class="mt-2" />
            </div>

            <div>
                <x-label for="billDueDate" value="{{ __('modules.utilityBills.billDueDate') }}" required="true "/>
                <x-datepicker class="block w-full mt-1" wire:model.live="billDueDate" id="billDueDate" autocomplete="off" placeholder="{{ __('modules.utilityBills.chooseBillDueDate') }}" />
                <x-input-error for="billDueDate" class="mt-2" />
            </div>

            <div x-data="{ photoName: null, photoPreview: null, isPdf: false }" x-on:photo-bill-removed.window="photoName = null; photoPreview = null; isPdf = false;">
                <!-- File Input -->
                <input type="file" id="billProof" class="hidden" wire:model="billProof" accept="image/png, image/gif, image/jpeg, image/webp, application/pdf"
                    x-ref="profilePhoto"
                    x-on:change="
                        photoName = $refs.profilePhoto.files[0].name;
                        const file = $refs.profilePhoto.files[0];
                        isPdf = file.type === 'application/pdf';
                        if (isPdf) {
                            photoPreview = null; // No preview for PDF
                        } else {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                photoPreview = e.target.result;
                            };
                            reader.readAsDataURL(file);
                        }
                    "/>

                <!-- Button to trigger file input -->
                <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.profilePhoto.click()">
                    {{ __('modules.utilityBills.billProof') }}
                </x-secondary-button>

                <!-- Preview for Images -->
                <div class="mt-2" x-show="photoPreview">
                    <span class="block w-20 h-20 bg-center bg-no-repeat bg-cover rounded-full"
                        x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <!-- Preview for PDFs -->
                <div class="mt-2" x-show="isPdf">
                    <span class="block text-sm text-gray-500 mt-1" x-text="photoName"></span>
                </div>

                <!-- Remove Button -->
                    <div class="mt-2" x-show="photoPreview || photoName">
                        <x-danger-button type="button" wire:click="removeBillPhoto">
                            {{ __('modules.utilityBills.removeBillProof') }}
                        </x-danger-button>
                    </div>

                <!-- Error Message -->
                <x-input-error for="profilePhoto" class="mt-2" />
            </div>

            <div>
                <x-label for="status" value="{{ __('modules.settings.status') }}"/>
                <x-select id="status" class="block w-full mt-1" wire:model.live="status">
                    <option value="unpaid">{{ __('modules.utilityBills.unpaid') }}</option>
                    <option value="paid">{{ __('modules.utilityBills.paid') }}</option>
                </x-select>
                <x-input-error for="status" class="mt-2" />
            </div>
            @if($status == "paid")
                <div x-data="{ photoName: null, photoPreview: null, isImage: true }" x-on:photo-removed.window="photoName = null; photoPreview = null; isImage = true;">
                    <input type="file" id="paymentProof" class="hidden" wire:model="paymentProof" accept="image/png, image/gif, image/jpeg, image/webp, application/pdf"
                        x-ref="profilePhoto"
                        x-on:change="
                            const file = $refs.profilePhoto.files[0];
                            photoName = file.name;
                            if (file.type.startsWith('image/')) {
                                isImage = true;
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    photoPreview = e.target.result;
                                };
                                reader.readAsDataURL(file);
                            } else if (file.type === 'application/pdf') {
                                isImage = false;
                                photoPreview = null;  // No image preview for PDFs
                            }
                        "/>

                    <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.profilePhoto.click()">
                        {{ __('modules.utilityBills.paymentProof') }}
                    </x-secondary-button>

                    <!-- Image Preview -->
                    <div class="mt-2" x-show="isImage && photoPreview">
                        <span class="block w-20 h-20 bg-center bg-no-repeat bg-cover rounded-full"
                            x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                        </span>
                    </div>

                    <!-- PDF File Name -->
                    <div class="mt-2" x-show="!isImage && photoName">
                        <span class="block text-sm text-gray-500" x-text="photoName"></span>
                    </div>
                    <!-- Remove Button -->
                    <div class="mt-2" x-show="photoPreview || photoName">
                        <x-danger-button type="button" wire:click="removeProfilePhoto">
                            {{ __('modules.utilityBills.removePaymentProof') }}
                        </x-danger-button>
                    </div>

                    <x-input-error for="profilePhoto" class="mt-2" />
                </div>

                <div>
                    <x-label for="billPaymentDate" value="{{ __('modules.utilityBills.billPaymentDate') }}" required="true"/>
                    <x-datepicker class="block w-full mt-1" wire:model.live="billPaymentDate" id="billPaymentDate" autocomplete="off" placeholder="{{ __('modules.utilityBills.chooseBillPaymentDate') }}" :maxDate="true"/>
                    <x-input-error for="billPaymentDate" class="mt-2" />
                </div>
            @endif
        </div>



        <div class="flex w-full pb-4 mt-6 space-x-4">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel  wire:click="$dispatch('hideAddUtilityBill')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>
