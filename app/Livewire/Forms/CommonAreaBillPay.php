<?php

namespace App\Livewire\Forms;

use App\Helper\Files;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Carbon;
use App\Models\CommonAreaBills;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Livewire\CommonAreaBill\CommonAreaBill;

class CommonAreaBillPay extends Component
{
    use WithFileUploads, LivewireAlert;

    public $file;
    public $fileUrl;
    public $showFileLink = false;
    public $paymentProof;
    public $commonAreaBill;
    public $status;
    public $billPaymentDate;
    public $billDate;
    public $showPayUtilityBillModal = false;
    public $id;
    public $selected;

    public function mount()
    {
        $this->status = $this->commonAreaBill->status;
        $this->paymentProof = $this->commonAreaBill->payment_proof;
        $this->billDate = $this->commonAreaBill->bill_date;
        $this->billPaymentDate = now()->format('Y-m-d');
    }

    public function submitForm()
    {
        $rules = [
            'billPaymentDate' => 'required|after_or_equal:billDate',
        ];

        $messages['billPaymentDate']['after'] = __('messages.billPaymentValidationMessage');

        $this->validate($rules, $messages);

        $this->commonAreaBill->bill_payment_date = $this->billPaymentDate;

        if ($this->paymentProof) {
            $filename = Files::uploadLocalOrS3($this->paymentProof, CommonAreaBills::FILE_PATH . '/');
            $this->paymentProof = $filename;
            $this->commonAreaBill->payment_proof = $this->paymentProof;
        }

        $this->commonAreaBill->status    = "paid";

        $this->commonAreaBill->save();

        $this->alert('success', __('messages.commonAreaBillPaid'));

        $this->dispatch('hidePayCommonAreaBill');
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
        return view('livewire.forms.common-area-bill-pay');
    }
}
