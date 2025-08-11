<?php

namespace App\Livewire\Forms;

use Carbon\Carbon;
use App\Helper\Files;
use Livewire\Component;
use App\Models\BillType;
use App\Models\CommonAreaBills;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EditCommonAreaBill extends Component
{
    public $billTypes;
    public $billAmount;
    public $billTypeId;
    public $apartmentId;
    public $billDate;
    public $status;
    public $billPaymentDate;
    public $billDueDate;
    public $paymentProof;
    public $commonAreaBill;
    public $hasNewPhoto = false;
    public $hasPhoto = false;
    public $billProof;

    public $image;

    use LivewireAlert, WithFileUploads;


    public function mount()
    {
        $this->billTypes = BillType::all();
        $this->billTypeId = $this->commonAreaBill->bill_type_id;
        $this->billAmount = $this->commonAreaBill->bill_amount;
        $this->billDate = Carbon::parse($this->commonAreaBill->bill_date)->format('d F Y');
        $this->billDueDate = Carbon::parse($this->commonAreaBill->bill_due_date)->format('d F Y');
        $this->status = $this->commonAreaBill->status;
        $this->billPaymentDate = $this->commonAreaBill->bill_payment_date;
        $this->paymentProof = $this->commonAreaBill->payment_proof;
        $this->billProof = $this->commonAreaBill->bill_proof;
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
            'billTypeId' => 'required',
            'billAmount' => 'required|min:0',
            'status' => 'required',
            'billDate' => 'required',
            'billPaymentDate' => 'required_if:status,paid',

        ]);

        $this->commonAreaBill->bill_type_id = $this->billTypeId;
        $this->commonAreaBill->bill_amount = $this->billAmount;
        $this->commonAreaBill->bill_date = $this->billDate;
        $this->commonAreaBill->bill_date = Carbon::parse($this->billDate)->format('Y-m-d');
        $this->commonAreaBill->bill_due_date = !empty($this->billDueDate)
            ? Carbon::parse($this->billDueDate)->format('Y-m-d')
            : null;
        $this->commonAreaBill->status = $this->status ?? 'unpaid';
        $this->commonAreaBill->bill_payment_date = !empty($this->billPaymentDate)
            ? Carbon::parse($this->billPaymentDate)->format('Y-m-d')
            : null;

        if ($this->billProof instanceof TemporaryUploadedFile) {
            $filename = Files::uploadLocalOrS3($this->billProof, CommonAreaBills::FILE_PATH . '/');
            $this->commonAreaBill->bill_proof = $filename;
            $this->hasNewPhoto = false;
        }

        if ($this->status == "paid") {
            if ($this->paymentProof instanceof TemporaryUploadedFile) {
                $filename = Files::uploadLocalOrS3($this->paymentProof, CommonAreaBills::FILE_PATH . '/');
                $this->commonAreaBill->payment_proof = $filename;
                $this->hasPhoto = false;
            }
        }
        $this->commonAreaBill->save();

        $this->alert('success', __('messages.commonAreaBillUpdated'));
        $this->dispatch('hideEditCommonAreaBill');
    }

    public function removeProfilePhoto()
    {
        $this->paymentProof = null;
        $this->commonAreaBill->payment_proof = null;
        $this->commonAreaBill->save();
        $this->dispatch('photo-removed');
    }

    public function removeBillPhoto()
    {
        $this->billProof = null;
        $this->commonAreaBill->bill_proof = null;
        $this->commonAreaBill->save();
        $this->dispatch('photo-bill-removed');
    }

    #[On('resetForm')]

    public function resetForm()
    {
        $this->resetValidation();
        $this->reset(['billTypeId', 'billAmount', 'billDate', 'status', 'billPaymentDate', 'paymentProof', 'billDueDate', 'billProof']);
    }

    public function render()
    {
        return view('livewire.forms.edit-common-area-bill');
    }
}
