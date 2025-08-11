<?php

namespace App\Livewire\Maintenance;

use App\Models\Tenant;
use App\Models\Payment;
use App\Models\Society;
use Livewire\Component;
use Livewire\Attributes\On;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\MaintenanceApartment;
use App\Exports\MaintenanceApartmentExport;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Currency;

class MaintenanceApartmentTable extends Component
{
    use LivewireAlert;

    public $search;
    public $maintenance;
    public $showPayModal;
    public $apartment_maintenance;
    public $clearFilterButton = false;
    public $showFilters = false;
    public $filterYears = [];
    public $filterMonths = [];
    public $filterStatus = [];
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;
    public $showSelectedPayModal = false;
    public $paymentDate;
    public $maintenanceApartmentDetailData;
    public $currencySymbol;

    public function mount()
    {
        $societyId = society()->id;
        $this->paymentDate = now()->format('Y-m-d');
        $userId = user()->id;
        $this->maintenanceApartmentDetailData = MaintenanceApartment::query()->with(['maintenanceManagement', 'apartment'])
            ->whereHas('apartment', function ($subQuery) use ($userId) {
                $subQuery->where('user_id', $userId);
            })
            ->whereHas('maintenanceManagement', function ($subQuery) {
                $subQuery->where('status', 'published');
            })
            ->get();
        $currencyId = Society::where('id', $societyId)->value('currency_id');
        $this->currencySymbol = Currency::find($currencyId)->currency_code;

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
    }


    #[On('showMaintenanceFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }

    public function clearFilters()
    {
        $this->filterYears = [];
        $this->filterMonths = [];
        $this->filterStatus = [];
        $this->search = '';
        $this->clearFilterButton = false;
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

    public function getMonthsProperty()
    {
        return [
            'january' => __('modules.rent.january'),
            'february' => __('modules.rent.february'),
            'march' => __('modules.rent.march'),
            'april' => __('modules.rent.april'),
            'may' => __('modules.rent.may'),
            'june' => __('modules.rent.june'),
            'july' => __('modules.rent.july'),
            'august' => __('modules.rent.august'),
            'september' => __('modules.rent.september'),
            'october' => __('modules.rent.october'),
            'november' => __('modules.rent.november'),
            'december' => __('modules.rent.december'),
        ];
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->maintenanceApartmentDetailData->pluck('id')->toArray() : [];
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

    public function paySelected()
    {
        $rules = [
            'paymentDate' => 'required',
        ];
        $this->validate($rules);
        foreach ($this->selected as $item) {
            $userId = user()->id;
            $maintenancePay = MaintenanceApartment::query()->with(['maintenanceManagement', 'apartment'])
            ->whereHas('apartment', function ($subQuery) use ($userId) {
                $subQuery->where('user_id', $userId);
            })
            ->whereHas('maintenanceManagement', function ($subQuery) {
                $subQuery->where('status', 'published');
            })
            ->where('paid_status','unpaid')->find($item);
            if ($maintenancePay) {
                $maintenancePay->payment_date = $this->paymentDate;
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

    #[On('exportMaintenanceApartment')]
    public function exportMaintenanceApartment()
    {
        return (new MaintenanceApartmentExport($this->search, $this->filterYears, $this->filterMonths, $this->filterStatus))->download('maintenance-apartment-'.now()->toDateTimeString().'.xlsx');
    }


    public function render()
    {
        $this->clearFilterButton = false;

        $userId = user()->id;

        $query = MaintenanceApartment::query()
        ->with(['maintenanceManagement', 'apartment', 'tenants']);
        if (isRole() == 'Owner') {
            $query->whereHas('apartment', function ($subQuery) use ($userId) {
                $subQuery->where('user_id', $userId);
            });
        }
        if (isRole() == 'Tenant') {
            $tenant = Tenant::where('user_id', $userId)->first();
            if ($tenant) {
                $query = $query->join('apartment_tenant', 'apartment_tenant.apartment_id', 'maintenance_apartment.apartment_management_id')
                ->where('apartment_tenant.tenant_id', $tenant->id)->where('apartment_tenant.status', 'current_resident')->select('maintenance_apartment.*');
            }
        }
        $query = $query->whereHas('maintenanceManagement', function ($subQuery) {
            $subQuery->where('status', 'published');
        });

        if ($this->search != '') {
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterYears)) {
            $query->whereHas('maintenanceManagement', function ($subQuery) {
                $subQuery->whereIn('year', $this->filterYears);
            });
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterMonths)) {
            $query->whereHas('maintenanceManagement', function ($subQuery) {
                $subQuery->whereIn('month', $this->filterMonths);
            });
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterStatus)) {
            $query->whereIn('paid_status', $this->filterStatus);
            $this->clearFilterButton = true;
        }

        if ($this->search) {
            $query->where(function ($mainQuery) {
                $mainQuery->where('paid_status', 'like', '%' . $this->search . '%')
                    ->orWhereHas('maintenanceManagement', function ($subQuery) {
                        $subQuery->where('year', 'like', '%' . $this->search . '%')
                            ->orWhere('month', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $maintenances = $query->paginate(10);

        return view('livewire.maintenance.maintenance-apartment-table', [
            'maintenances' => $maintenances
        ]);

    }
}
