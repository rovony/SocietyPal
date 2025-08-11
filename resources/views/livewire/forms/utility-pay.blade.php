<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">
            <div class = "mt-3">
                <x-label for="billPaymentDate" value="{{ __('modules.utilityBills.billPaymentDate') }}" required="true" />
                <x-datepicker class="block w-full mt-1" wire:model.live="billPaymentDate" id="billPaymentDate" autocomplete="off" placeholder="{{ __('modules.utilityBills.chooseBillPaymentDate') }}" :maxDate="true"/>
                <x-input-error for="billPaymentDate" class="mt-2" />
            </div>

            <div>
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
            </div>
            <div class="flex justify-end w-full pb-4 space-x-4 mt-9">
                <x-button>@lang('app.save')</x-button>
            </div>
        </div>
    </form>
</div>
