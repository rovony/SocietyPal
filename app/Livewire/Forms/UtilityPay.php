<?php

namespace App\Livewire\Forms;

use App\Helper\Files;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Carbon;
use App\Models\UtilityBillManagement;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class UtilityPay extends Component
{

    use WithFileUploads, LivewireAlert;
    public $file;
    public $fileUrl;
    public $showFileLink = false;
    public $paymentProof;
    public $utilityBill;
    public $status;
    public $billPaymentDate;
    public $showPayUtilityBillModal = false;
    public $id;

    public function mount()
    {
        $this->status = $this->utilityBill ? $this->utilityBill->status : null;
        $this->paymentProof = $this->utilityBill ? $this->utilityBill->payment_proof : null;
        $this->billPaymentDate = now()->format('Y-m-d');
    }

    public function submitForm()
    {
        $this->validate([
            'billPaymentDate' => 'required',
        ]);

        $this->utilityBill->bill_payment_date = $this->billPaymentDate;

        if ($this->paymentProof) {
            $filename = Files::uploadLocalOrS3($this->paymentProof, UtilityBillManagement::FILE_PATH . '/');
            $this->paymentProof = $filename;
            $this->utilityBill->payment_proof    = $this->paymentProof;
        }

        $this->utilityBill->status    = "paid";

        $this->utilityBill->save();

        $this->alert('success', __('messages.utilityBillPaid'));

        $this->dispatch('hidePayUtilityBill');
    }


    #[On('resetFileData')]
    public function resetFileData()
    {

        $this->paymentProof = '';
        $this->resetValidation();
    }

    public function removeProfilePhoto()
    {
        $this->paymentProof = null;
        $this->dispatch('photo-removed');
    }

    public function render()
    {
        return view('livewire.forms.utility-pay');
    }
}
