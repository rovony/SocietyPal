<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">
            <div class = "mt-3">
                <x-label for="paymentDate" value="{{ __('modules.rent.paymentDate') }}" required="true" />
                <x-datepicker class="block w-full mt-1" wire:model.live="paymentDate" id="paymentDate" autocomplete="off" placeholder="{{ __('modules.rent.choosePaymentDate') }}" :maxDate="true"/>
                <x-input-error for="paymentDate" class="mt-2" />
            </div>

            <div>
                <div x-data="{ photoName: null, photoPreview: null }" x-on:photo-removed.window="photoName = null; photoPreview = null;">
                    <input type="file" id="paymentProof" class="hidden" wire:model="paymentProof" accept="image/png, image/gif, image/jpeg, image/webp"
                        x-ref="profilePhoto"
                        x-on:change="
                            photoName = $refs.profilePhoto.files[0].name;
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                photoPreview = e.target.result;
                            };
                            reader.readAsDataURL($refs.profilePhoto.files[0]);
                        "/>
                    <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.profilePhoto.click()">
                        {{ __('modules.utilityBills.paymentProof') }}
                    </x-secondary-button>

                    <div class="mt-2" x-show="photoPreview">
                        <span class="block w-20 h-20 bg-center bg-no-repeat bg-cover rounded-full"
                            x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                        </span>
                    </div>

                    <div class="mt-2" x-show="photoPreview">
                        <x-danger-button type="button" wire:click="removeProfilePhoto">
                            {{ __('modules.utilityBills.removePaymentProof') }}
                        </x-danger-button>
                    </div>

                    <x-input-error for="profilePhoto" class="mt-2" />
                </div>
            </div>
            <div class="flex w-full pb-4 mt-6 space-x-4">
                <x-button>@lang('app.save')</x-button>
            </div>
        </div>
    </form>
</div>
