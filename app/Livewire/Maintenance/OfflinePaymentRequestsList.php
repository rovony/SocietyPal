<?php

namespace App\Livewire\Maintenance;

use Carbon\Carbon;
use App\Models\Payment;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\MaintenanceApartment;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class OfflinePaymentRequestsList extends Component
{
    use LivewireAlert;
    public $offlinePaymentRequest;
    public $offlinePlanChange;
    public $showConfirmChangeModal = false;
    public $status;
    public $payDate;
    public $showViewRequestModal = false;
    public $selectViewRequest;
    public $billProof;



    public function mount()
    {
        $this->offlinePaymentRequest = MaintenanceApartment::where('paid_status', 'payment_requested')
        ->whereHas('maintenanceManagement', function ($query) {
            $query->where('society_id', user()->society_id);
        })
        ->get();
    }

    public function ViewRequest($id)
    {
        $this->selectViewRequest = MaintenanceApartment::findOrFail($id);
        $this->showViewRequestModal = true;
        $this->billProof = $this->selectViewRequest->payment_proof_url;

    }

    #[On('confirmChangePlan')]
    public function confirmChangePlan($id, $status)
    {
        $this->offlinePlanChange = MaintenanceApartment::findOrFail($id);

        if ($status == 'paid') {
            $this->offlinePlanChange->paid_status = 'paid';
            $this->offlinePlanChange->payment_date = now()->format('Y-m-d');
            $this->offlinePlanChange->save();

            $payment = new Payment();
            $payment->maintenance_apartment_id = $id;
            $payment->payment_method = 'offline';
            $payment->amount =  $this->offlinePlanChange->cost;
            $payment->save();

            $this->alert('success', __('messages.paymentAcceptedSuccessfully.'));
        } elseif ($status == 'unpaid') {
            $this->offlinePlanChange->paid_status = 'unpaid';
            $this->offlinePlanChange->save();
            $this->alert('success', __('messages.paymentDeclinedSuccessfully.'));
        }
        $this->js("Livewire.navigate(window.location.href)");

        $this->showConfirmChangeModal = true;
    }

    public function render()
    {
        return view('livewire.maintenance.offline-payment-requests-list');
    }
}
