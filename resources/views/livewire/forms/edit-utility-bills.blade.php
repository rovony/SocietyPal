<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">
            <div>
                <x-search-dropdown id="apartmentId" label="{{ __('modules.settings.societyApartmentNumber') }}" model="apartmentId" :options="$apartments->map(fn($item) => ['id' => $item->id, 'number' => $item->apartment_number])->toArray()" placeholder="{{ __('modules.utilityBills.selectApartmentNumber') }}" required="true" :disabled="$status == 'paid'"/>
            </div>

            <div>
                <x-label for="billTypeId" :value="__('modules.settings.billType')" required="true" />
                <x-select id="billTypeId" class="block w-full mt-1" wire:model="billTypeId" :disabled="($status == 'paid')">
                    <option value="">@lang('modules.utilityBills.selectBillType')</option>
                    @foreach ($billTypes as $item)
                        <option value="{{ $item->id }}">{{ $item->name}}</option>
                    @endforeach
                </x-select>
                <x-input-error for="billTypeId" class="mt-2" />
            </div>

            <div>
                <x-label for="billAmount" value="{{ __('modules.utilityBills.billAmount') }}" required="true" />
                <x-input id="billAmount" class="block w-full mt-1" min='0' step="0.01" type="number" wire:model='billAmount' :disabled="($status == 'paid')"/>
                <x-input-error for="billAmount" class="mt-2" />
            </div>

            <div>
                <x-label for="billDate" value="{{ __('modules.utilityBills.billDate') }}" required="true"/>
                <x-datepicker class="block w-full mt-1" wire:model.live="billDate" :disabled="($status == 'paid')" id="billDate" autocomplete="off" placeholder="{{ __('modules.utilityBills.chooseBillDate') }}" />
                <x-input-error for="billDate" class="mt-2" />
            </div>

            <div>
                <x-label for="billDueDate" value="{{ __('modules.utilityBills.billDueDate') }}" required="true "/>
                <x-datepicker class="block w-full mt-1" wire:model.live="billDueDate" id="billDueDate" autocomplete="off" placeholder="{{ __('modules.utilityBills.chooseBillDueDate') }}" />
                <x-input-error for="billDueDate" class="mt-2" />
            </div>

            <div x-data="{
                photoName: null,
                photoPreview: null,
                hasNewPhoto: @entangle('hasNewPhoto').live,
                clearFileInput() {
                    this.photoName = '';
                    this.photoPreview = '';
                    this.hasNewPhoto = false;
                    this.$refs.billProof.value = '';
                },
                isPdf() {
                    return this.photoName?.endsWith('.pdf');
                }
                }" class="col-span-6 sm:col-span-4">

                <input type="file" class="hidden"
                    wire:model="billProof"
                    accept="image/png, image/gif, image/jpeg, image/webp, application/pdf"
                    x-ref="billProof"
                    x-on:change="
                        photoName = $refs.billProof.files[0].name;
                        const reader = new FileReader();
                        if (photoName.endsWith('.pdf')) {
                            photoPreview = null; // No preview for PDFs
                        } else {
                            reader.onload = (e) => {
                                photoPreview = e.target.result;
                            };
                            reader.readAsDataURL($refs.billProof.files[0]);
                        }
                        hasNewPhoto = true;" />

                <x-label for="photoName" value="{{ __('Bill Proof') }}" />

                <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.billProof.click()">
                    {{ __('modules.utilityBills.billProof') }}
                </x-secondary-button>

                <!-- Show PDF name for uploaded files -->
                <div class="mt-2" x-show="isPdf()" style="display: none;">
                    <span class="block text-sm text-gray-500 mt-1" x-text="photoName"></span>
                </div>

                <!-- Show image preview for uploaded files -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block w-20 h-20 bg-center bg-no-repeat bg-cover rounded-full"
                        x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <!-- Show PDF name or image preview for saved files -->
                @if ($utilityBill->bill_proof)
                    <div class="mt-2" x-show="!photoPreview">
                        @if (pathinfo($utilityBill->bill_proof_url, PATHINFO_EXTENSION) === 'pdf')
                            <span class="block text-sm text-gray-500 mt-1">{{ basename($utilityBill->bill_proof_url) }}</span>
                        @else
                            <img src="{{ $utilityBill->bill_proof_url }}" alt="{{ __('Bill Proof') }}" class="object-cover w-20 h-20 overflow-hidden rounded-full">
                        @endif
                    </div>
                @endif

                <x-danger-button class="mt-2" type="button" x-on:click.prevent="clearFileInput()" x-show="hasNewPhoto" x-cloak>
                    {{ __('modules.utilityBills.removeBillProof') }}
                </x-danger-button>

                @if ($utilityBill->bill_proof)
                    <x-danger-button type="button" class="mt-2" wire:click="removeBillPhoto" x-on:click.prevent="clearFileInput()" x-show="!hasNewPhoto" x-cloak>
                        {{ __('modules.utilityBills.removeBillProof') }}
                    </x-danger-button>
                @endif
            </div>

            <div>
                <x-label for="status" value="{{ __('modules.settings.status') }}" required="true" />
                <x-select id="status" class="block w-full mt-1" wire:model.live="status" :disabled="($status == 'paid')">
                    <option value="">@lang('modules.settings.selectStatus')</option>
                    <option value="unpaid">{{ __('modules.utilityBills.unpaid') }}</option>
                    <option value="paid">{{ __('modules.utilityBills.paid') }}</option>
                </x-select>
                <x-input-error for="status" class="mt-2" />
            </div>

            @if($status == "paid")
                <div x-data="{
                    photoName: null,
                    photoPreview: null,
                    hasPhoto: @entangle('hasPhoto').live,
                    clearFileInput() {
                        this.photoName = '';
                        this.photoPreview = '';
                        this.hasPhoto = false;
                        this.$refs.paymentProof.value = '';
                    },
                    isPdf() {
                        return this.photoName?.endsWith('.pdf');
                    }
                    }" class="col-span-6 sm:col-span-4">

                    <input type="file" class="hidden"
                        wire:model="paymentProof"
                        accept="image/png, image/gif, image/jpeg, image/webp, application/pdf"
                        x-ref="paymentProof"
                        x-on:change="
                            photoName = $refs.paymentProof.files[0].name;
                            const reader = new FileReader();
                            if (photoName.endsWith('.pdf')) {
                                photoPreview = null; // No preview for PDFs
                            } else {
                                reader.onload = (e) => {
                                    photoPreview = e.target.result;
                                };
                                reader.readAsDataURL($refs.paymentProof.files[0]);
                            }
                            hasPhoto = true;" />

                    <x-label for="photoName" value="{{ __('Payment Proof') }}" />

                    <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.paymentProof.click()">
                        {{ __('modules.utilityBills.paymentProof') }}
                    </x-secondary-button>

                    <!-- Show PDF name for uploaded files -->
                    <div class="mt-2" x-show="isPdf()" style="display: none;">
                        <span class="block text-sm text-gray-500 mt-1" x-text="photoName"></span>
                    </div>

                    <!-- Show image preview for uploaded files -->
                    <div class="mt-2" x-show="photoPreview" style="display: none;">
                        <span class="block w-20 h-20 bg-center bg-no-repeat bg-cover rounded-full"
                            x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                        </span>
                    </div>

                    <!-- Show PDF name or image preview for saved files -->
                    @if ($utilityBill->payment_proof)
                        <div class="mt-2" x-show="!photoPreview">
                            @if (pathinfo($utilityBill->payment_proof_url, PATHINFO_EXTENSION) === 'pdf')
                                <span class="block text-sm text-gray-500 mt-1">{{ basename($utilityBill->payment_proof_url) }}</span>
                            @else
                                <img src="{{ $utilityBill->payment_proof_url }}" alt="{{ __('Payment Proof') }}" class="object-cover w-20 h-20 overflow-hidden rounded-full">
                            @endif
                        </div>
                    @endif

                    <x-danger-button class="mt-2" type="button" x-on:click.prevent="clearFileInput()" x-show="hasPhoto" x-cloak>
                        {{ __('modules.utilityBills.removePaymentProof') }}
                    </x-danger-button>

                    @if ($utilityBill->payment_proof)
                        <x-danger-button type="button" class="mt-2" wire:click="removeProfilePhoto" x-on:click.prevent="clearFileInput()" x-show="!hasPhoto" x-cloak>
                            {{ __('modules.utilityBills.removePaymentProof') }}
                        </x-danger-button>
                    @endif
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
            <x-button-cancel  wire:click="$dispatch('hideEditUtilityBill')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>
