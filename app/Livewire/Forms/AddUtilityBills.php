<?php

namespace App\Livewire\Forms;

use App\Helper\Files;
use Livewire\Component;
use App\Models\BillType;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Carbon;
use App\Models\UtilityBillManagement;
use App\Models\ApartmentManagement;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Notifications\UtilityBillNotification;
use Illuminate\Support\Facades\Notification;

class AddUtilityBills extends Component
{
    use LivewireAlert, WithFileUploads;

    public $apartments;
    public $billTypes;
    public $billAmount;
    public $billTypeId;
    public $apartmentId;
    public $billDate;
    public $status;
    public $billPaymentDate;
    public $paymentProof;
    public $billProof;
    public $billDueDate;

    public function mount()
    {
        $this->apartments = ApartmentManagement::whereNotIn('status', ['not_sold'])->get();
        $this->billTypes = BillType::whereNotIn('bill_type_category', [BillType::COMMON_AREA_BILL_TYPE])->get();
    }

    public function updatedStatus()
    {

        if ($this->status == "unpaid") {
            $this->billPaymentDate = null;
            $this->paymentProof = null;
        }
    }

    public function submitForm()
    {
        $this->validate([
            'apartmentId' => 'required',
            'billTypeId' => 'required',
            'billAmount' => 'required|min:0',
            'billDate' => 'required',
            'billPaymentDate' => 'required_if:status,paid',
        ]);

        $utilityBill = new UtilityBillManagement();
        $utilityBill->apartment_id = $this->apartmentId;
        $utilityBill->bill_type_id = $this->billTypeId;
        $utilityBill->bill_amount = $this->billAmount;
        $utilityBill->bill_date = $this->billDate;
        $utilityBill->status = $this->status ?? "unpaid";
        $utilityBill->bill_payment_date    = $this->billPaymentDate;
        $utilityBill->bill_due_date = !empty($this->billDueDate)
            ? Carbon::parse($this->billDueDate)->format('Y-m-d')
            : null;

        if ($this->paymentProof) {
            $filename = Files::uploadLocalOrS3($this->paymentProof, UtilityBillManagement::FILE_PATH . '/');
            $this->paymentProof = $filename;
        }

        $utilityBill->payment_proof    = $this->paymentProof;

        if ($this->billProof) {
            $filename = Files::uploadLocalOrS3($this->billProof, UtilityBillManagement::FILE_PATH . '/');
            $this->billProof = $filename;
        }

        $utilityBill->bill_proof = $this->billProof;

        $utilityBill->save();
        try {
            $apartment = ApartmentManagement::with(['user', 'tenants.user'])->findOrFail($this->apartmentId);
            
            if ($apartment->user) {
                $apartment->user->notify(new UtilityBillNotification($utilityBill));
            }
    
            foreach ($apartment->tenants as $tenant) {
                if ($tenant->user) {
                    $tenant->user->notify(new UtilityBillNotification($utilityBill));
                }
            }
    
            $this->alert('success', __('messages.utilityBillAdded'));
    
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end',
            ]);
        }

        $this->alert('success', __('messages.utilityBillAdded'));

        $this->dispatch('hideAddUtilityBill');
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->resetValidation();
        $this->reset(['apartmentId', 'billTypeId', 'billAmount', 'billDate', 'status', 'billPaymentDate', 'paymentProof', 'billDueDate', 'billProof']);
    }

    public function removeProfilePhoto()
    {
        $this->paymentProof = null;
        $this->dispatch('photo-removed');
    }

    public function removeBillPhoto()
    {
        $this->billProof = null;
        $this->dispatch('photo-bill-removed');
    }

    public function render()
    {
        return view('livewire.forms.add-utility-bills');
    }
}
