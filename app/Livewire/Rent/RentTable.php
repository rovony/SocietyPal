<?php

namespace App\Livewire\Rent;

use App\Exports\RentsExport;
use App\Models\Rent;
use App\Models\Society;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;

class RentTable extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;
    protected $listeners = ['refreshUsers' => '$refresh'];

    public $search;
    public $seletedRent;
    public $showRentDetailModal = false;
    public $showEditRentModal = false;
    public $confirmDeleteRentModal = false;
    public $rent;
    public $showPayModal = false;
    public $showFilters = false;
    public $filterYears = [];
    public $filterMonths = [];
    public $filterStatuses = [];
    public $clearFilterButton = false;
    public $names;
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;
    public $rentsData;
    public $confirmSelectedDeleteRentModal = false;
    public $showSelectedPayModal = false;
    public $paymentDate;
    public $payRent;

    public function mount()
    {
        $this->rentsData = Rent::get();
        $this->paymentDate = now()->format('Y-m-d');
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

    public function showEditRent($id)
    {
        $this->seletedRent = Rent::findOrFail($id);
        $this->showEditRentModal = true;
    }

    #[On('hideEditRent')]
    public function hideEditRent()
    {
        $this->showEditRentModal = false;
        $this->js('window.location.reload()');
    }


    public function showDeleteRent($id)
    {
        $this->seletedRent = Rent::findOrFail($id);
        $this->confirmDeleteRentModal = true;
    }

    public function deleteRent($id)
    {
        Rent::destroy($id);
        $this->confirmDeleteRentModal = false;
        $this->seletedRent = '';

        $this->alert('success', __('messages.rentDeleted'));

    }

    public function showSelectedDeleteRent()
    {
        $this->confirmSelectedDeleteRentModal = true;
    }

    public function setStatus($status, $rentId)
    {
        $rent = Rent::find($rentId);

        if ($rent) {
            $rent->status = $status;
            $rent->save();
        }

        $this->redirect(route('rents.index'), navigate: true);
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

    #[On('showRentFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }

    public function clearFilters()
    {
        $this->filterYears = [];
        $this->filterMonths = [];
        $this->filterStatuses = [];
        $this->search = '';
        $this->dispatch('clearRentFilter');
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
        $this->selected = $value ? $this->rentsData->pluck('id')->toArray() : [];
        $this->showActions = count($this->selected) > 0;
    }

    public function updatedSelected()
    {
        $this->showActions = count($this->selected) > 0;
    }

    public function deleteSelected()
    {
        Rent::whereIn('id', $this->selected)->where('status', 'unpaid')->delete();
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions=false;
        $this->confirmSelectedDeleteRentModal=false;
        $this->alert('success', __('messages.rentDeleted'));
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
            $rent = Rent::where('status','unpaid')->find($item);
            if ($rent) {
                $rent->payment_date = \Carbon\Carbon::parse($this->paymentDate)->format('Y-m-d');
                $rent->status = "paid";
                $rent->save();
            }
        }
        $this->alert('success', __('messages.rentPaid'));
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions = false;
        $this->showSelectedPayModal = false;
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

    #[On('exportRents')]
    public function exportRents()
    {
        return (new RentsExport($this->search, $this->filterYears, $this->filterMonths, $this->filterStatuses))->download('rents-'.now()->toDateTimeString().'.xlsx');
    }

    public function render()
    {
        $query = Rent::with('tenant.user', 'apartment')
            ->where(function ($q) {
                $q->whereHas('tenant.user', function ($subQuery) {
                    $subQuery->where('name', 'like', '%'.$this->search.'%');
                })
                ->orWhere('status', 'like', '%'.$this->search.'%')
                ->orWhere('rent_for_year', 'like', '%'.$this->search.'%')
                ->orWhere('rent_for_month', 'like', '%'.$this->search.'%')
                ->orWhere('rent_amount', 'like', '%'.$this->search.'%');
            });

        $this->clearFilterButton = false;

        if (!empty($this->filterYears)) {
            $query = $query->whereIn('rent_for_year', $this->filterYears);
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterMonths)) {
            $query = $query->whereIn('rent_for_month', $this->filterMonths);
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterStatuses)) {
            $query = $query->whereIn('status', $this->filterStatuses);
            $this->clearFilterButton = true;
        }

        if (!user_can('Show Rent')) {
            if (isRole() == 'Owner') { 
                $query->whereHas('apartment', function ($q) {
                    $q->where('user_id', user()->id);
                });
            }
            if (isRole() == 'Tenant') {
                $query->whereHas('tenant', function ($q) {
                    $q->where('user_id', user()->id)
                      ->whereHas('apartments', function ($q) {
                          $q->where('apartment_tenant.status', 'current_resident');
                      });
                });
            }
        }

        $rents = $query->paginate(10);

        return view('livewire.rent.rent-table', [
            'rents' => $rents,
        ]);
    }

}
