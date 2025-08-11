<?php

namespace App\Livewire\Tenants;

use App\Models\ApartmentManagement;
use App\Models\Tenant;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditApartmentTenant extends Component
{
    use LivewireAlert;

    public $tenantId;
    public $apartmentId;
    public $apartment_id;
    public $contract_start_date;
    public $contract_end_date;
    public $rent_amount;
    public $rent_billing_cycle;
    public $status;
    public $move_in_date;
    public $move_out_date;
    public $apartmentRented;
    public $disableStatusSelect = false;

    public function mount()
    {
        $this->tenantId = $this->tenantId;

        $tenant = Tenant::findOrFail($this->tenantId);
        $this->apartment_id = $this->apartmentId->id;
        $apartmentData = $tenant->apartments()
        ->where('apartment_tenant.apartment_id', $this->apartment_id)
        ->firstOrFail();
        $this->contract_start_date = $apartmentData->pivot->contract_start_date;
        $this->contract_end_date = $apartmentData->pivot->contract_end_date;
        $this->rent_amount = $apartmentData->pivot->rent_amount;
        $this->rent_billing_cycle = $apartmentData->pivot->rent_billing_cycle;
        $this->status = $apartmentData->pivot->status;
        $this->move_in_date = $apartmentData->pivot->move_in_date;
        $this->move_out_date = $apartmentData->pivot->move_out_date;

        $this->apartmentRented = ApartmentManagement::where(function ($query) {
            $query->whereNotIn('status', ['occupied', 'rented'])
                ->orWhere('id', $this->apartment_id);
        })->get();
    }

    public function updateApartment()
    {
        $formattedContractStartDate = $this->contract_start_date ? Carbon::parse($this->contract_start_date)->format('Y-m-d') : null;
        $formattedContractEndDate = $this->contract_end_date ? Carbon::parse($this->contract_end_date)->format('Y-m-d') : null;
        $formattedMoveInDate = $this->move_in_date ? Carbon::parse($this->move_in_date)->format('Y-m-d') : null;
        $formattedMoveOutDate = $this->move_out_date ? Carbon::parse($this->move_out_date)->format('Y-m-d') : null;

        if ($this->status === 'left') {
            $formattedContractEndDate = Carbon::now()->format('Y-m-d');
        
            if ($formattedMoveOutDate !== null) {
                $formattedMoveOutDate = Carbon::now()->format('Y-m-d');
            }
        }

        $this->validate(
            [
                'rent_amount' => 'nullable|numeric|min:0',
                'apartment_id' => 'required',
                'contract_start_date' => 'required|date',
                'contract_end_date' => 'required|date|after:contract_start_date',
                'move_in_date' => [
                    'nullable',
                    'date',
                    function ($attribute, $value, $fail) use ($formattedContractStartDate, $formattedContractEndDate, $formattedMoveInDate) {
                        if ($formattedContractStartDate && $formattedMoveInDate && $formattedMoveInDate < $formattedContractStartDate) {
                            $fail(__('messages.moveInDateAfterError'));
                        }

                        if ($formattedContractEndDate && $formattedMoveInDate && $formattedMoveInDate > $formattedContractEndDate) {
                            $fail(__('messages.moveOutDateBeforeError'));
                        }
                    }
                ],
                'move_out_date' => [
                    'nullable',
                    'date',
                    'after:move_in_date',
                    function ($attribute, $value, $fail) use ($formattedContractStartDate, $formattedContractEndDate, $formattedMoveOutDate) {
                        if ($value && !$this->move_in_date) {
                            $fail(__('messages.moveOutDateRequiresMoveInDate'));
                        }
                        if ($formattedContractStartDate && $formattedMoveOutDate && $formattedMoveOutDate < $formattedContractStartDate) {
                            $fail(__('messages.moveOutDateAfterError'));
                        }
                        if ($formattedContractEndDate && $formattedMoveOutDate && $formattedMoveOutDate > $formattedContractEndDate) {
                            $fail(__('messages.moveOutDateBeforeError'));
                        }
                    }
                ],
            ]);

        $tenant = Tenant::find($this->tenantId);
        $apartment = ApartmentManagement::find($this->apartment_id);

        if ($this->status === 'current_resident') {
            if ($apartment->status === 'rented') {
                $alreadyTaken = $tenant->apartments()
                    ->wherePivot('apartment_id', $this->apartment_id)
                    ->wherePivot('status', '!=', 'current_resident') 
                    ->exists();
    
                if ($alreadyTaken) {
                    $this->disableStatusSelect = true;
                    return;
                }
            }
    
            if ($apartment->status === 'available_for_rent') {
                $apartment->status = 'rented';
                $apartment->save();
    
                if ($tenant->user) {
                    $tenant->user->status = 'active';
                    $tenant->user->save();
                }
            }
        }
    
        $tenant->apartments()->updateExistingPivot($this->apartment_id, [
            'contract_start_date' => $formattedContractStartDate,
            'contract_end_date' => $formattedContractEndDate,
            'rent_amount' => $this->rent_amount,
            'rent_billing_cycle' => $this->rent_billing_cycle,
            'status' => $this->status,
            'move_in_date' => $formattedMoveInDate,
            'move_out_date' => $formattedMoveOutDate,
        ]);


        if ($this->status === 'left') {
            $apartment->status = $apartment->user_id ? 'available_for_rent' : 'not_sold';
        } elseif ($this->status === 'current_resident') {
            $apartment->status = 'rented';
        }
    
        $apartment->save();    
        
        if ($this->status === 'left') {
            $hasCurrent = $tenant->apartments()->where('apartment_tenant.status', 'current_resident')->exists();
            if (!$hasCurrent && $tenant->user) {
                $tenant->user->status = 'inactive';
                $tenant->user->save();
            }
        }

        $this->alert('success', __('messages.apartmentManagementUpdated'));
        return redirect()->route('tenants.show', ['tenant' => $tenant->id]);
    }
    public function render()
    {
        return view('livewire.tenants.edit-apartment-tenant');
    }
}
