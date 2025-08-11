<?php

namespace App\Livewire\Forms;

use App\Models\Society;
use Livewire\Component;
use App\Models\Currency;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ShowUtilityBills extends Component
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
    public $image;
    public $currencySymbol;
    public $billAmountId;

    use LivewireAlert, WithFileUploads;


    public function mount()
    {
        $this->apartmentId = $this->utilityBill->apartment->apartment_number;
        $this->billTypeId = $this->utilityBill->billType ? $this->utilityBill->billType->name : '--';
        $this->billAmount =$this->utilityBill->bill_amount;
        $this->billAmountId =$this->utilityBill->id;
        $this->billDate =$this->utilityBill->bill_date;
        $this->billDueDate =$this->utilityBill->bill_due_date;
        $this->status =$this->utilityBill->status;
        $this->billPaymentDate =$this->utilityBill->bill_payment_date;
        $this->paymentProof =$this->utilityBill->payment_proof_url;
        $this->billProof =$this->utilityBill->bill_proof_url;
        $societyId = society()->id;
        $currencyId = Society::where('id', $societyId)->value('currency_id');
        $this->currencySymbol = Currency::find($currencyId)->currency_symbol;
    }

    public function download()
    {
        $filePath = $this->utilityBill->payment_proof;

        if($filePath){
            return Storage::disk(config('filesystems.default'))->download($filePath);
        }

    }

    public function render()
    {
        return view('livewire.forms.show-utility-bills');
    }
}
