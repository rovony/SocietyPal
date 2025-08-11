<?php

namespace App\Livewire\Tenants;

use App\Models\ApartmentManagement;
use App\Models\Tenant;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class AddApartmentTenant extends Component
{
    use LivewireAlert, WithFileUploads;

    public $tenantId;
    public $apartment_id;
    public $contract_start_date;
    public $contract_end_date;
    public $rent_amount;
    public $rent_billing_cycle = 'monthly';
    public $status = 'current_resident';
    public $move_in_date;
    public $move_out_date;
    public $apartmentRented;

    public function mount()
    {
        $this->tenantId = $this->tenantId;
        $this->apartmentRented = ApartmentManagement::whereNotIn('status', ['occupied', 'rented'])->get();
    }

    public function addApartment()
    {
        $this->validate(
            [
                'rent_amount' => 'nullable|numeric|min:0',
                'apartment_id' => 'required',
                'contract_start_date' => 'required|date',
                'contract_end_date' => 'required|date|after:contract_start_date',
                'move_in_date' => [
                    'nullable',
                    'date',
                    function ($attribute, $value, $fail) {
                        if ($this->contract_start_date && $value < $this->contract_start_date) {
                            $fail(__('messages.moveInDateAfterError'));
                        }
                        if ($this->contract_end_date && $value > $this->contract_end_date) {
                            $fail(__('messages.moveOutDateBeforeError'));
                        }
                    }
                ],
                'move_out_date' => [
                    'nullable',
                    'date',
                    'after:move_in_date',
                    function ($attribute, $value, $fail) {
                        if ($value && !$this->move_in_date) {
                            $fail(__('messages.moveOutDateRequiresMoveInDate'));
                        }
                        if ($this->contract_start_date && $value < $this->contract_start_date) {
                            $fail(__('messages.moveOutDateAfterError'));
                        }
                        if ($this->contract_end_date && $value > $this->contract_end_date) {
                            $fail(__('messages.moveOutDateBeforeError'));
                        }
                    }
                ],
            ],
        );

        $tenant = Tenant::find($this->tenantId);
        $formattedContractStartDate = $this->contract_start_date ? Carbon::parse($this->contract_start_date)->format('Y-m-d') : null;
        $formattedContractEndDate = $this->contract_end_date ? Carbon::parse($this->contract_end_date)->format('Y-m-d') : null;
        $formattedMoveInDate = $this->move_in_date ? Carbon::parse($this->move_in_date)->format('Y-m-d') : null;
        $formattedMoveOutDate = $this->move_out_date ? Carbon::parse($this->move_out_date)->format('Y-m-d') : null;

        $existing = $tenant->apartments()->wherePivot('apartment_id', $this->apartment_id)->exists();

        if ($existing) {
            $tenant->apartments()->updateExistingPivot($this->apartment_id, [
                'contract_start_date' => $formattedContractStartDate,
                'contract_end_date' => $formattedContractEndDate,
                'rent_amount' => $this->rent_amount,
                'rent_billing_cycle' => $this->rent_billing_cycle,
                'status' => $this->status,
                'move_in_date' => $formattedMoveInDate,
                'move_out_date' => $formattedMoveOutDate,
            ]);
        } else {
            $tenant->apartments()->attach($this->apartment_id, [
                'contract_start_date' => $formattedContractStartDate,
                'contract_end_date' => $formattedContractEndDate,
                'rent_amount' => $this->rent_amount,
                'rent_billing_cycle' => $this->rent_billing_cycle,
                'status' => $this->status,
                'move_in_date' => $formattedMoveInDate,
                'move_out_date' => $formattedMoveOutDate,
            ]);
        }
        ApartmentManagement::where('id', $this->apartment_id)->update(['status' => 'rented']);
        if ($tenant->user && $tenant->user->status !== 'active') {
            $hasApartment = $tenant->apartments()->exists();
            if ($hasApartment) {
                $tenant->user->status = 'active';
                $tenant->user->save();
            }
        }

        $this->resetForm();
        $this->alert('success', __('messages.tenantAdded'));
        return redirect()->route('tenants.show', ['tenant' => $tenant->id]);

    }

    public function resetForm()
    {
        $this->contract_start_date = '';
        $this->contract_end_date = '';
        $this->rent_amount = '';
        $this->rent_billing_cycle = '';
        $this->status = '';
        $this->move_in_date = '';
        $this->move_out_date = '';
    }

    public function render()
    {
        return view('livewire.tenants.add-apartment-tenant');
    }
}
