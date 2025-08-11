<?php

namespace App\Livewire\Maintenance;

use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Society;
use Livewire\Component;
use App\Models\Currency;
use Livewire\Attributes\On;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ApartmentManagement;
use App\Models\MaintenanceApartment;
use App\Models\MaintenanceManagement;
use App\Exports\MaintenanceDetailExport;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Notifications\MaintenancePublishedNotification;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Payments;

class MaintenanceDetail extends Component
{
    use WithFileUploads ,LivewireAlert;

    public $maintenance;
    public $maintenanceId;
    public $totalApartments;
    public $paidApartments;
    public $showPayModal;
    public $apartment_maintenance;
    public $showFilterButton = true;
    public $clearFilterButton = false;
    public $showFilters = false;
    public $filterApartments = [];
    public $filterStatus = [];
    public $apartmentList = [];
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;
    public $showSelectedPayModal = false;
    public $paymentDate;
    public $maintenanceDetailData;
    public $currencySymbol;

    public function mount($maintenanceId)
    {
        $this->maintenanceId = $maintenanceId;
        $this->maintenance = MaintenanceManagement::with(['maintenanceApartments', 'maintenance'])->findOrFail($this->maintenanceId);
        $this->totalApartments = $this->maintenance->maintenanceApartments->count();
        $this->paidApartments = $this->maintenance->maintenanceApartments->where('paid_status', 'paid')->count();
        $societyId = society()->id;
        $this->apartmentList = ApartmentManagement::all();
        $this->paymentDate = now()->format('Y-m-d');
        $this->maintenanceDetailData = MaintenanceApartment::where('maintenance_management_id', $this->maintenanceId)->get();
        $societyId = society()->id;
        $currencyId = Society::where('id', $societyId)->value('currency_id');
        $this->currencySymbol = Currency::find($currencyId)->currency_code;

    }

    public function publishMaintenance()
    {
        $this->maintenance->load('maintenanceApartments.apartment.tenants.user');
        $this->maintenance->status = 'published';
        $this->maintenance->save();

        try {
            foreach ($this->maintenance->maintenanceApartments as $maintenanceApartment) {
                $apartment = $maintenanceApartment->apartment;

                if ($apartment->user) {
                    $apartment->user->notify(new MaintenancePublishedNotification($this->maintenance, $maintenanceApartment->cost));
                }

                foreach ($apartment->tenants as $tenant) {
                    if ($tenant->user) {
                        $tenant->user->notify(new MaintenancePublishedNotification($this->maintenance, $maintenanceApartment->cost));
                    }
                }
            }

            $this->alert('success', __('messages.maintenancePublished'));
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end'
            ]);
        }
    }


    public function showPay($id)
    {
        $this->apartment_maintenance = MaintenanceApartment::findOrFail($id);
        $this->showPayModal = true;
    }

    #[On('hidePay')]
    public function hidePay()
    {
        $this->showPayModal = false;
        $this->showSelectedPayModal = false;
    }

    #[On('showMaintenanceFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }

    public function clearFilters()
    {
        $this->filterApartments = [];
        $this->filterStatus = [];
        $this->dispatch('clearMaintenanceFilter');
    }

    #[On('clearMaintenanceFilter')]
    public function clearMaintenanceFilter()
    {
        $this->showFilterButton = false;
    }

    #[On('hideMaintenanceFilters')]
    public function hideMaintenanceFiltersBtn()
    {
        $this->showFilterButton = true;
    }

    public function export()
    {
        return (new MaintenanceDetailExport($this->maintenanceId, $this->filterApartments, $this->filterStatus))->download('maintenance-detail-'.now()->toDateTimeString().'.xlsx');
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->maintenanceDetailData->pluck('id')->toArray() : [];
        $this->showActions = count($this->selected) > 0;
    }

    public function updatedSelected()
    {
        $this->showActions = count($this->selected) > 0;
    }

    public function showSelectedPay()
    {
        $this->selected = $this->selected;
        $this->showSelectedPayModal = true;
    }

    public function downloadReceipt($id)
    {

        $payment = Payment::where('maintenance_apartment_id', $id)->first();
        $society = Society::first();

        if (!$payment) {
            $this->alert('error', __('messages.noMaintenanceFound'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
            return;
        }
        $pdf = Pdf::loadView('maintenance.maintenance-bill-receipt', [
            'payment' => $payment,
            'currencySymbol' => $this->currencySymbol,
            'society' => $society
        ]);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'maintenance-receipt-' . uniqid() . '.pdf');
    }

    public function paySelected()
    {
        $rules = [
            'paymentDate' => 'required',
        ];
        $this->validate($rules);
        $formattedPaymentDate = Carbon::parse($this->paymentDate)->format('Y-m-d');
        foreach ($this->selected as $item) {
            $maintenancePay = MaintenanceApartment::where('maintenance_management_id', $this->maintenanceId)->where('paid_status','unpaid')->find($item);
            if ($maintenancePay) {
                $maintenancePay->payment_date = $formattedPaymentDate;
                $maintenancePay->paid_status = "paid";
                $maintenancePay->save();
            }
        }
        $this->alert('success', __('messages.maintenancePaid'));
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions = false;
        $this->showSelectedPayModal = false;
    }

    public function render()
    {
        $this->clearFilterButton = false;

        $query = MaintenanceApartment::query()->where('maintenance_management_id', $this->maintenanceId)->with('apartment');

        if (!empty($this->filterApartments)) {
            $query->whereHas('apartment', function ($query) {
                $query->whereIn('apartment_number', $this->filterApartments);
            });
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterStatus)) {
            $query->whereIn('paid_status', $this->filterStatus);
            $this->clearFilterButton = true;
        }

        $apartments = $query->paginate(10);

        return view('livewire.maintenance.maintenance-detail', [
            'apartments' => $apartments,
        ]);
    }
}
