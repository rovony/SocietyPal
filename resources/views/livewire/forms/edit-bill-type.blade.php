<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">

            <div>
                <x-label for="billTypeName" value="{{ __('modules.settings.editbillType') }}" required="true" />
                <x-input id="billTypeName" class="block w-full mt-1" type="text" wire:model='billTypeName' />
                <x-input-error for="billTypeName" class="mt-2" />
            </div>

            <div>
                <x-label for="billTypeCategory" :value="__('modules.settings.billCategory')" required="true" />
                <x-select id="billTypeCategory" class="block w-full mt-1" wire:model="billTypeCategory">
                    <option value="">@lang('modules.utilityBills.selectBillType')</option>
                    <option value="{{ \App\Models\BillType::COMMON_AREA_BILL_TYPE }}">{{ __('modules.commonAreaBill.commonAreaBillType') }}</option>
                    <option value="{{ \App\Models\BillType::UTILITY_BILL_TYPE }}">{{ __('modules.utilityBills.utilityBillsType') }}</option>
                </x-select>
                <x-input-error for="billTypeCategory" class="mt-2" />
            </div>

        </div>

        <div class="flex w-full pb-4 mt-6 space-x-4">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel  wire:click="$dispatch('hideEditBillType')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>
