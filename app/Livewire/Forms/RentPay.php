<?php

namespace App\Livewire\Forms;

use App\Helper\Files;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Rent;

class RentPay extends Component
{
    use WithFileUploads, LivewireAlert;

    public $file;
    public $fileUrl;
    public $showFileLink = false;
    public $paymentProof;
    public $rent;
    public $status;
    public $paymentDate;
    public $showPayRentModal = false;
    public $id;

    public function mount()
    {
        $this->status = $this->rent->status;
        $this->paymentProof = $this->rent ? $this->rent->payment_proof : null;
        $this->paymentDate = now()->format('Y-m-d');
    }

    public function submitForm()
    {
        $this->validate([
            'paymentDate' => 'required',
        ]);

        $this->paymentDate = Carbon::parse($this->paymentDate)->format('Y-m-d');
        $this->rent->payment_date = $this->paymentDate;

        if ($this->paymentProof) {
            $filename = Files::uploadLocalOrS3($this->paymentProof, Rent::FILE_PATH . '/');
            $this->paymentProof = $filename;
            $this->rent->payment_proof = $this->paymentProof;
        }
        $this->rent->status = "paid";
        $this->rent->save();

        $this->alert('success', __('messages.rentPaid'));

        $this->dispatch('hidePayRent');
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
        return view('livewire.forms.rent-pay');
    }
}
