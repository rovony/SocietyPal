<?php

namespace App\Livewire\Tenants;

use App\Models\ParkingManagementSetting;
use App\Models\Rent;
use App\Models\Society;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Barryvdh\DomPDF\Facade\Pdf;

class TenantDetail extends Component
{
    use WithFileUploads , LivewireAlert;

    public $tenant;
    public $tenantId;
    public $documentName = '';
    public $documentFile;
    public $documents = [];
    public $activeTab = 'apartment';
    public $parkings;
    public $showPayModal = false;
    public $payRent;
    public $seletedRent;
    public $showRentDetailModal = false;
    protected $queryString = ['activeTab'];

    public function mount()
    {
        $this->tenantId = $this->tenantId;
        $this->tenant = Tenant::with('user', 'documents', 'apartments.user', 'rents.apartment' , 'rents.tenant')->findOrFail($this->tenantId);
        $this->documents = $this->tenant->documents()->get(['id', 'filename', 'hashname'])->toArray();
        $apartmentIds = $this->tenant->apartments()->pluck('apartment_managements.id');

        $this->parkings = ParkingManagementSetting::whereHas('apartmentManagement', function ($query) use ($apartmentIds) {
            $query->whereIn('apartment_parking.apartment_management_id', $apartmentIds);
        })->get();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function impersonate($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            $this->alert('error', 'No User found this society', [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);

            return true;
        }

        $admin = user();
        session()->flush();
        session()->forget('user');

        Auth::guard('web')->logout();
        session(['impersonate_user_id' => $admin->id]);
        session(['user' => $user]);
        session()->flush();
        session(['impersonate_user_id' => $admin->id]);

        Auth::guard('web')->loginUsingId($user->id);

        return $this->redirect(route('dashboard'));
    }

    public function showPay($id)
    {
        $this->payRent = Rent::findOrFail($id);
        $this->showPayModal = true;
    }

    #[On('hidePayRent')]
    public function hidePayRent()
    {
        $this->showPayModal = false;
    }

    public function showRentDetail($id)
    {
        $this->seletedRent = Rent::findOrFail($id);
        $this->showRentDetailModal = true;
    }

    #[On('hideRentDetail')]
    public function hideRentDetail()
    {
        $this->showRentDetailModal = false;
    }

    public function downloadReceipt($id)
    {
        $rent = Rent::findOrFail($id);
        $society = Society::with('currency')->first();
        if (!$rent) {
            $this->alert('error', __('messages.noBillFound'));
            return;
        }


        $pdf = Pdf::loadView('rents.billing-receipt', ['rent' => $rent, 'society' => $society]);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'rent-bill-receipt-' . uniqid() . '.pdf');
    }

    public function render()
    {
        return view('livewire.tenants.tenant-detail');
    }
}
