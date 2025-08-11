<?php

namespace App\Livewire\Forms;

use App\Helper\Files;
use Livewire\Component;
use App\Models\BillType;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use App\Models\CommonAreaBills;
use App\Models\ApartmentManagement;
use Illuminate\Support\Facades\Notification;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Notifications\CommonAreaBillNotification;

class AddCommonAreaBill extends Component
{

    use LivewireAlert, WithFileUploads;

    public $billTypes;
    public $billAmount;
    public $billTypeId;
    public $billDate;
    public $status;
    public $billPaymentDate;
    public $billDueDate;
    public $paymentProof;
    public $billProof;

    protected $listeners = ['fetchDefault' => 'mount'];

    public function mount()
    {
        $this->billTypes = BillType::whereNotIn('bill_type_category', ['Utility Bill Type'])->get();
        $this->billDate = now()->format('d F Y');
        $this->billPaymentDate = now()->format('d F Y');

    }

    public function updatedStatus(){

        if ($this->status == "unpaid") {
            $this->billPaymentDate =null;
            $this->paymentProof = null;

        }
    }

    public function updatedBillDate()
    {
        $this->billPaymentDate = null;
    }

    public function submitForm()
    {
        $rules = [
            'billTypeId' => 'required',
            'billAmount' => 'required|min:0',
            'billDate' => 'required|date',
            'billDueDate' => 'nullable|date|after_or_equal:billDate',
            'billPaymentDate' => 'required_if:status,paid|nullable|after_or_equal:billDate',
        ];

        $messages = [
            'billPaymentDate.after_or_equal' => __('messages.billPaymentValidationMessage'),
        ];

        $this->validate($rules, $messages);

        $commonAreaBill = new CommonAreaBills();
        $commonAreaBill->bill_type_id = $this->billTypeId;
        $commonAreaBill->bill_amount = $this->billAmount;
        $commonAreaBill->bill_date = Carbon::parse($this->billDate)->format('Y-m-d');
        $commonAreaBill->bill_due_date = !empty($this->billDueDate)
            ? Carbon::parse($this->billDueDate)->format('Y-m-d')
            : null;
        $commonAreaBill->status = $this->status ?? "unpaid";
        $commonAreaBill->bill_payment_date = !empty($this->billPaymentDate)
            ? Carbon::parse($this->billPaymentDate)->format('Y-m-d')
            : null;

        if ($this->paymentProof) {
            $filename = Files::uploadLocalOrS3($this->paymentProof, CommonAreaBills::FILE_PATH .'/');
            $commonAreaBill->payment_proof = $filename;
        }


        if ($this->billProof) {
            $filename = Files::uploadLocalOrS3($this->billProof, CommonAreaBills::FILE_PATH .'/');
            $this->billProof = $filename;
        }

        $commonAreaBill->bill_proof = $this->billProof;

        $commonAreaBill->save();

        $this->alert('success', __('messages.commonAreaBillAdded'));
        $this->dispatch('hideAddCommonAreaBill');
    }


    #[On('resetForm')]
    public function resetForm()
    {
        $this->resetValidation();
        $this->dispatch('fetchDefault');
        $this->reset(['billTypeId','billAmount','billDate','status','billPaymentDate','paymentProof','billDueDate','billProof']);
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
        return view('livewire.forms.add-common-area-bill');
    }
}
