<?php

namespace App\Livewire\CommonAreaBill;

use Carbon\Carbon;
use App\Helper\Files;
use App\Models\Society;
use Livewire\Component;
use App\Models\BillType;
use App\Models\Currency;
use Livewire\Attributes\On;
use App\Models\CommonAreaBills;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\CommonAreaBillExport;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class CommonAreaBill extends Component
{

    use LivewireAlert;

    protected $listeners = ['refreshCommonAreaBills' => 'mount'];

    public $billTypes;
    public $search;
    public $commonAreaBill;
    public $commonAreaBills;
    public $currencySymbol;
    public $showEditCommonAreaBillModal = false;
    public $showCommonAreaBillModal = false;
    public $showAddCommonAreaBillModal = false;
    public $confirmDeleteCommonAreaBillModal = false;
    public $confirmSelectedDeleteCommonAreaBillModal = false;
    public $showPayModal = false;
    public $showSelectedPayModal = false;
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;
    public $showFilters = false;
    public $clearFilterButton = false;
    public $startDate;
    public $endDate;
    public $filterBillTypes = [];
    public $billPaymentDate;
    public $filterStatuses = [];

    public function mount()
    {
        $this->billTypes = BillType::get();
        $societyId = society()->id;
        $currencyId = Society::where('id', $societyId)->value('currency_id');
        $this->currencySymbol = Currency::find($currencyId)->currency_code;
        $this->commonAreaBills = CommonAreaBills::get();
    }

    public function showAddCommonAreaBill()
    {
        $this->dispatch('resetForm');
        $this->showAddCommonAreaBillModal = true;
    }

    public function showEditCommonAreaBill($id)
    {
        $this->commonAreaBill = CommonAreaBills::findOrFail($id);
        $this->showEditCommonAreaBillModal = true;
    }

    public function showCommonAreaBill($id)
    {
        $this->commonAreaBill = CommonAreaBills::findOrFail($id);
        $this->showCommonAreaBillModal = true;
    }

    #[On('hideCommonAreaBillModal')]
    public function hideCommonAreaBillModal(){
        $this->commonAreaBill ="";
        $this->showCommonAreaBillModal = false;
    }

    public function showDeleteCommonAreaBill($id)
    {
        $this->commonAreaBill = CommonAreaBills::findOrFail($id);
        $this->confirmDeleteCommonAreaBillModal = true;
    }

    public function deleteCommonAreaBill($id)
    {
        CommonAreaBills::destroy($id);

        $this->confirmDeleteCommonAreaBillModal = false;

        $this->commonAreaBill= '';
        $this->dispatch('refreshCommonAreaBills');

        $this->alert('success', __('messages.commonAreaBillDeleted'));
    }

    #[On('hideEditCommonAreaBill')]
    public function hideEditCommonAreaBill()
    {
        $this->showEditCommonAreaBillModal = false;
        $this->js('window.location.reload()');
    }

    #[On('hideShowCommonAreaBill')]
    public function hideShowCommonAreaBill()
    {
        $this->showCommonAreaBillModal = false;
        $this->dispatch('refreshCommonAreaBills');
    }

    public function showPay($id)
    {
        $this->commonAreaBill = CommonAreaBills::findOrFail($id);
        $this->showPayModal = true;
    }


    #[On('hidePayCommonAreaBill')]
    public function hidePayUCommonAreaBill()
    {
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions=false;
        $this->showPayModal = false;
        $this->showSelectedPayModal = false;
        $this->dispatch('refreshCommonAreaBills');
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->commonAreaBills->pluck('id')->toArray() : [];
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

    public function showSelectedDelete()
    {
        $this->confirmSelectedDeleteCommonAreaBillModal = true;
    }

    public function deleteSelected()
    {
        CommonAreaBills::whereIn('id', $this->selected)->where('status','unpaid')->delete();
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions=false;
        $this->confirmSelectedDeleteCommonAreaBillModal = false;
        $this->alert('success', __('messages.commonAreaBillDeleted'));

    }

    #[On('showCommonAreaBillFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }

    public function paySelected()
    {
        $rules = [
            'billPaymentDate' => 'required|after_or_equal:billDate',
        ];

        $messages = [
            'billPaymentDate.after_or_equal' => __('messages.billPaymentValidationMessage'),
        ];

        $this->validate($rules, $messages);

        foreach ($this->selected as $item) {
            $commonAreaBill = CommonAreaBills::find($item);

            if ($commonAreaBill) {
                $commonAreaBill->bill_payment_date = $this->billPaymentDate;
                $commonAreaBill->status = "paid";
                $commonAreaBill->save();
            }
        }

        $this->alert('success', __('messages.commonAreaBillPaid'));
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions = false;
        $this->showSelectedPayModal = false;
    }



    public function clearFilters()
    {
        $this->startDate = '';
        $this->endDate = '';
        $this->search = '';
        $this->filterBillTypes = [];
        $this->filterStatuses = [];

    }

    public function downloadReceipt($id)
    {
        $commonAreaBill = CommonAreaBills::with('billType')->findOrFail($id);
        $society = Society::first();
        if (!$commonAreaBill) {

            $this->alert('error', __('messages.noBillFound'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);

            return;
        }

        $pdf = Pdf::loadView('common_area_bills.billing-receipt', ['commonAreaBill' => $commonAreaBill, 'currencySymbol'=> $this->currencySymbol, 'society' => $society]);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'common-bill-receipt-' . uniqid() . '.pdf');
    }

    public function billDownload($id)
    {
        $commonAreaBill = CommonAreaBills::with('billType')->findOrFail($id);

        $filePath = $commonAreaBill->bill_proof;

        if ($filePath) {
            return response()->streamDownload(function () use ($filePath) {
                echo Storage::disk(config('filesystems.default'))->get($filePath);
            }, basename($filePath));
        }
    }

    #[On('exportCommonAreaBill')]
    public function exportCommonAreaBill()
    {
        return (new CommonAreaBillExport($this->search, $this->startDate, $this->endDate, $this->filterBillTypes, $this->filterStatuses))->download('commonArea-'.now()->toDateTimeString().'.xlsx');
    }

    public function render()
    {
        $this->clearFilterButton = false;

        $query = CommonAreaBills::query()
            ->with('billType');

        // Search filter
        if (!empty($this->search)) {
            $query->where(function ($subQuery) {
                $subQuery->whereHas('billType', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('status', 'like', '%' . $this->search . '%');
            });
        }

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

        $commonAreaBillData = $query->paginate(10);

        return view('livewire.common-area-bill.common-area-bill', [
            'commonAreaBillData' => $commonAreaBillData,
        ]);
    }
}
