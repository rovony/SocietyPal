<?php

namespace App\Livewire\Forms;

use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class ShowRent extends Component
{
    public $tenant;
    public $apartment;
    public $rent_amount;
    public $status;
    public $payment_date;
    public $paymentProof;
    public $rent;

    use LivewireAlert, WithFileUploads;

    public function mount($rent)
    {
        $this->tenant = $rent->tenant;
        $this->apartment = $rent->apartment;
        $this->rent_amount = $rent->rent_amount;
        $this->status = $rent->status;
        $this->payment_date = $rent->payment_date;
        $this->paymentProof = $rent->payment_proof_url;
    }

    public function download()
    {
        $filePath = $this->rent->payment_proof;

        if($filePath){
            return Storage::disk(config('filesystems.default'))->download($filePath);
        }

    }

    public function render()
    {
        return view('livewire.forms.show-rent');
    }
}
