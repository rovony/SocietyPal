<?php

namespace App\Livewire\UtilityBills;

use Carbon\Carbon;
use App\Models\Society;
use Livewire\Component;
use App\Models\BillType;
use App\Models\Currency;
use App\Models\Apartment;
use Livewire\Attributes\On;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\UtilityBillExport;
use App\Models\ApartmentManagement;
use App\Models\UtilityBillManagement;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class UtilityBillsManagement extends Component
{
    use LivewireAlert;

    protected $listeners = ['refreshUtilityBills' => 'mount'];

    public $apartments;
    public $search;
    public $utilityBill;
    public $utilityBills;
    public $billTypes;
    public $tower;
    public $currencySymbol;
    public $showEditUtilityBillModal = false;
    public $showUtilityBillModal = false;
    public $showAddUtilityBillModal = false;
    public $confirmDeleteUtilityBillModal = false;
    public $confirmSelectedDeleteUtilityBillModal = false;
    public $showPayModal = false;
    public $showSelectedPayModal = false;
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;
    public $showFilters = false;
    public $clearFilterButton = false;
    public $startDate;
    public $endDate;
    public $filterApartments = [];
    public $filterBillTypes = [];
    public $billPaymentDate;
    public $filterStatuses = [];

    public function mount()
    {
        $this->apartments = ApartmentManagement::get();
        $this->apartments = ApartmentManagement::get();
        $this->utilityBills = UtilityBillManagement::get();
        $this->billTypes = BillType::get();
        $societyId = society()->id;
        $currencyId = Society::where('id', $societyId)->value('currency_id');
        $this->currencySymbol = Currency::find($currencyId)->currency_code;
    }

    public function showAddUtilityBill()
    {
        $this->dispatch('resetForm');
        $this->showAddUtilityBillModal = true;
    }

    public function showEditUtilityBill($id)
    {
        $this->utilityBill = UtilityBillManagement::findOrFail($id);
        $this->showEditUtilityBillModal = true;
    }

    public function showUtilityBill($id)
    {
        $this->utilityBill = UtilityBillManagement::findOrFail($id);
        $this->showUtilityBillModal = true;
    }

    #[On('hideUtilityBillModal')]
    public function hideUtilityBillModal(){
        $this->utilityBill ="";
        $this->showUtilityBillModal = false;
    }

    public function showDeleteUtilityBill($id)
    {
        $this->utilityBill = UtilityBillManagement::findOrFail($id);
        $this->confirmDeleteUtilityBillModal = true;
    }

    public function deleteUtilityBill($id)
    {
        UtilityBillManagement::destroy($id);

        $this->confirmDeleteUtilityBillModal = false;

        $this->utilityBill= '';
        $this->dispatch('refreshUtilityBills');

        $this->alert('success', __('messages.utilityBillDeleted'));
    }

    #[On('hideEditUtilityBill')]
    public function hideEditUtilityBill()
    {
        $this->showEditUtilityBillModal = false;
        $this->js('window.location.reload()');
    }

    #[On('hideShowUtilityBill')]
    public function hideShowUtilityBill()
    {
        $this->showUtilityBillModal = false;
        $this->dispatch('refreshUtilityBills');
    }

    public function showPay($id)
    {
        $this->utilityBill = UtilityBillManagement::findOrFail($id);
        $this->showPayModal = true;
    }

    #[On('hidePayUtilityBill')]
    public function hidePayUtilityBill()
    {
        $this->showPayModal = false;
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions=false;
        $this->showPayModal = false;
        $this->showSelectedPayModal = false;
        $this->dispatch('refreshUtilityBills');
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->utilityBills->pluck('id')->toArray() : [];
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
            'billPaymentDate' => 'required|after_or_equal:billDate',
        ];

        $messages = [
            'billPaymentDate.after_or_equal' => "The bill payment date field must be after or equal to the bill date.",
        ];

        $this->validate($rules, $messages);

        foreach ($this->selected as $item) {
            $utilityBills = UtilityBillManagement::where('status','unpaid')->find($item);

            if ($utilityBills) {
                $utilityBills->bill_payment_date = $this->billPaymentDate;
                $utilityBills->status = "paid";
                $utilityBills->save();
            }
        }

        $this->alert('success', __('messages.utilityBillPaid'));
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions = false;
        $this->showSelectedPayModal = false;
    }

    public function showSelectedDelete()
    {
        $this->confirmSelectedDeleteUtilityBillModal = true;
    }

    public function deleteSelected()
    {
        UtilityBillManagement::whereIn('id', $this->selected)->where('status','unpaid')->delete();
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions=false;
        $this->confirmSelectedDeleteUtilityBillModal = false;
        $this->alert('success', __('messages.utilityBillDeleted'));

    }

    #[On('showUtilityBillsFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }

    public function clearFilters()
    {
        $this->startDate = '';
        $this->endDate = '';
        $this->search = '';
        $this->filterApartments = [];
        $this->filterBillTypes = [];
        $this->filterStatuses = [];
    }

    public function downloadReceipt($id)
    {
        $utility = UtilityBillManagement::with('billType')->findOrFail($id);
        $society = Society::first();
        if (!$utility) {

            $this->alert('error', __('messages.noBillFound'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);

            return;
        }


        $pdf = Pdf::loadView('utilityBills.billing-receipt', ['utility' => $utility, 'currencySymbol'=> $this->currencySymbol, 'society' => $society]);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'utility-bill-receipt-' . uniqid() . '.pdf');
    }

    public function billDownload($id)
    {
        $utility = UtilityBillManagement::with('billType')->findOrFail($id);

        $filePath = $utility->bill_proof;
        if ($filePath) {
            return response()->streamDownload(function () use ($filePath) {
                echo Storage::disk(config('filesystems.default'))->get($filePath);
            }, basename($filePath));
        }
    }


    #[On('exportUitilityBill')]
    public function exportUitilityBill()
    {
        return (new UtilityBillExport($this->search, $this->startDate, $this->endDate, $this->filterApartments, $this->filterBillTypes, $this->filterStatuses))->download('utility-'.now()->toDateTimeString().'.xlsx');
    }

    public function render()
    {
        $this->clearFilterButton = false;

        $query = UtilityBillManagement::query();
        $loggedInUser = user()->id;

        if (!user_can('Show Utility Bills')) {
            $query->where(function ($q) use ($loggedInUser) {
                $q->whereHas('apartment', function ($q) use ($loggedInUser) {
                    $q->where('user_id', $loggedInUser);
                })
                ->orWhereHas('apartment', function ($q) use ($loggedInUser) {
                    $q->whereHas('tenants', function ($q) use ($loggedInUser) {
                        $q->where('user_id', $loggedInUser)
                        ->where('apartment_tenant.status', 'current_resident');
                    });
                });
            });
        }

        $query->with('apartment', 'billType')
            ->where(function ($query) {
                $query->whereHas('apartment', function ($q) {
                    $q->where('apartment_number', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('billType', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('status', 'like', '%' . $this->search . '%');
            });

        if ((!empty($this->startDate)) || (!empty($this->endDate))) {
            $this->clearFilterButton = true;

            if (!empty($this->startDate)) {
                $startDate = Carbon::parse($this->startDate)->format('Y-m-d');
                $query->where(function ($subQuery) use ($startDate) {
                    $subQuery->where('bill_date', '>=', $startDate)
                    ->orWhere('bill_payment_date', '>=', $startDate);
                });
            }

            if (!empty($this->endDate)) {
                $endDate = Carbon::parse($this->endDate)->format('Y-m-d');
                $query->where(function ($subQuery) use ($endDate) {
                    $subQuery->where('bill_payment_date', '<=', $endDate)
                    ->orWhere('bill_date', '<=', $endDate);
                });
            }
        }

        if (!empty($this->filterApartments)) {
            $query->whereHas('apartment', function ($query) {
                $query->whereIn('apartment_number', $this->filterApartments);
            });
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterBillTypes)) {
            $query->whereHas('billType', function ($query) {
                $query->whereIn('name', $this->filterBillTypes);
            });
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterStatuses)) {
            $this->clearFilterButton = true;
            $query->whereIn('status', $this->filterStatuses);
        }

        $utilityBillsData = $query->paginate(10);

        return view('livewire.utility-bills.utility-bills-management', [
            'utilityBillsData' => $utilityBillsData,
        ]);
    }

}
