<?php

namespace App\Livewire\Forms;

use App\Models\Society;
use Livewire\Component;
use App\Models\Currency;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ShowCommonAreaBill extends Component
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
    public $billDueDate;
    public $paymentProof;
    public $commonAreaBill;
    public $image;
    public $currencySymbol;
    public $billProof;
    public $billAmountId;

    public function mount()
    {
        $this->billTypeId = $this->commonAreaBill->billType ? $this->commonAreaBill->billType->name : '--';
        $this->billAmount =$this->commonAreaBill->bill_amount;
        $this->billDate =$this->commonAreaBill->bill_date;
        $this->status =$this->commonAreaBill->status;
        $this->billAmountId =$this->commonAreaBill->id;
        $this->billPaymentDate =$this->commonAreaBill->bill_payment_date;
        $this->billDueDate =$this->commonAreaBill->bill_due_date;
        $this->paymentProof =$this->commonAreaBill->payment_proof_url;
        $this->billProof =$this->commonAreaBill->bill_proof_url;
        $societyId = society()->id;
        $currencyId = Society::where('id', $societyId)->value('currency_id');
        $this->currencySymbol = Currency::find($currencyId)->currency_symbol;
    }

    public function render()
    {
        return view('livewire.forms.show-common-area-bill');
    }
}
