<?php

namespace App\Livewire\Forms;

use App\Helper\Files;
use Livewire\Component;
use App\Models\BillType;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Carbon;
use App\Models\ApartmentManagement;
use App\Models\UtilityBillManagement;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EditUtilityBills extends Component
{
    public $apartments;
    public $billTypes;
    public $billAmount;
    public $billTypeId;
    public $apartmentId;
    public $billDate;
    public $billDueDate;
    public $status;
    public $billPaymentDate;
    public $paymentProof;
    public $billProof;
    public $utilityBill;
    public $hasNewPhoto = false;
    public $hasPhoto = false;
    public $image;
    public $teamLogo;


    use LivewireAlert, WithFileUploads;


    public function mount()
    {
        $this->apartmentId = $this->utilityBill->apartment_id;
        $this->apartments = ApartmentManagement::all();
        $this->billTypes = BillType::all();
        $this->billTypeId = $this->utilityBill->bill_type_id;
        $this->billAmount = $this->utilityBill->bill_amount;
        $this->billDate = Carbon::parse($this->utilityBill->bill_date)->format('d F Y');
        $this->billDueDate = Carbon::parse($this->utilityBill->bill_due_date)->format('d F Y');
        $this->status = $this->utilityBill->status;
        $this->billPaymentDate = $this->utilityBill->bill_payment_date;
        $this->paymentProof = $this->utilityBill->payment_proof;
        $this->billProof = $this->utilityBill->bill_proof;
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
            'status' => 'required',
            'billDate' => 'required',
            'billPaymentDate' => 'required_if:status,paid',

        ]);

        $this->utilityBill->apartment_id = $this->apartmentId;
        $this->utilityBill->bill_type_id = $this->billTypeId;
        $this->utilityBill->bill_amount = $this->billAmount;
        $this->utilityBill->bill_date = $this->billDate;
        $this->utilityBill->bill_due_date = !empty($this->billDueDate)
            ? Carbon::parse($this->billDueDate)->format('Y-m-d')
            : null;
        $this->utilityBill->status = $this->status;
        $this->utilityBill->bill_payment_date = $this->billPaymentDate;

        if ($this->billProof instanceof TemporaryUploadedFile) {
            $filename = Files::uploadLocalOrS3($this->billProof, UtilityBillManagement::FILE_PATH . '/');
            $this->utilityBill->bill_proof = $filename;
            $this->hasNewPhoto = false;
        }

        if ($this->status == "paid") {
            if ($this->paymentProof instanceof TemporaryUploadedFile) {
                $filename = Files::uploadLocalOrS3($this->paymentProof, UtilityBillManagement::FILE_PATH . '/');
                $this->utilityBill->payment_proof = $filename;
                $this->hasPhoto = false;
            }
        }
        $this->utilityBill->save();

        $this->alert('success', __('messages.utilityBillUpdated'));

        $this->dispatch('hideEditUtilityBill');
    }

    public function removeProfilePhoto()
    {
        $this->paymentProof = null;
        $this->utilityBill->payment_proof = null;
        $this->utilityBill->save();
        $this->dispatch('photo-removed');
    }

    public function removeBillPhoto()
    {
        $this->billProof = null;
        $this->utilityBill->bill_proof = null;
        $this->utilityBill->save();
        $this->dispatch('photo-bill-removed');
    }

    #[On('resetForm')]

    public function resetForm()
    {
        $this->resetValidation();
        $this->reset(['apartmentId', 'billTypeId', 'billAmount', 'billDate', 'status', 'billPaymentDate', 'paymentProof', 'billProof']);
    }

    public function render()
    {
        return view('livewire.forms.edit-utility-bills');
    }
}
