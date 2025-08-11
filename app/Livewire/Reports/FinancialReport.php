<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\MaintenanceApartment;
use App\Models\MaintenanceManagement;
use App\Models\UtilityBillManagement;
use App\Models\Society;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\ApartmentManagement;
use App\Models\ApartmentTenant;
use Livewire\Attributes\On;

class FinancialReport extends Component
{
    public $reportType = 'month-wise'; 
    public $selectedMonth;
    public $selectedYear;
    public $months = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'];
    public $years = [];
    public $financialData = [];
    public $showFilterButton = true;
    public $clearFilterButton = false;
    public $showFilters = false;
    public $filterApartments = [];
    public $apartments;

    public function mount()
    {
        $this->years = range(date('Y') - 5, date('Y'));
        $this->selectedMonth = strtolower(date('F'));
        $this->selectedYear = date('Y');
        $this->financialData = $this->generateReport();
        $this->apartments = ApartmentManagement::get();
    }

    #[On('clearReportFilter')]
    public function clearUserFilter()
    {
        $this->showFilterButton = false;
    }

    #[On('hideReportFilters')]
    public function hideReportFiltersBtn()
    {
        $this->showFilterButton = true;
    }

     #[On('showReportFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }

    public function clearFilters()
    {
        $this->filterApartments = [];
        $this->clearFilterButton = false;
        $this->financialData = $this->generateReport();
    }

    public function updatedSelectedMonth()
    {
        $this->financialData = $this->generateReport();
    }

    public function updatedSelectedYear()
    {
        $this->financialData = $this->generateReport();
    }

    public function updatedReportType()
    {
        $this->financialData = $this->generateReport();
    }

    private function getUserApartmentIds()
    {
        $user = user();

        if (isRole() === 'Admin') {
            return null;
        }

        $ownerApartmentIds = ApartmentManagement::where('user_id', $user->id)->pluck('id')->toArray();

        $tenantApartmentIds = ApartmentTenant::whereHas('tenant', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->pluck('apartment_id')->toArray();

        return array_unique(array_merge($ownerApartmentIds, $tenantApartmentIds));
    }

    public function generateReport()
    {
        $data = [];
        $apartmentIds = $this->getUserApartmentIds();

        if ($this->reportType === 'month-wise') {
            $monthNumber = Carbon::parse("1 {$this->selectedMonth}")->month;
            
            $maintenanceManagements = MaintenanceManagement::where('month', $this->selectedMonth)
                ->where('year', $this->selectedYear)->where('status', 'published')
                ->get();

            foreach ($maintenanceManagements as $management) {
                $apartments = MaintenanceApartment::with('apartment')
                    ->where('maintenance_management_id', $management->id)
                    ->when($apartmentIds, fn ($q) => $q->whereIn('apartment_management_id', $apartmentIds))
                    ->get();

                foreach ($apartments as $item) {
                    $flat = $item->apartment->apartment_number;
                    $apartmentId = $item->apartment->id;

                    if (!isset($data[$flat])) {
                        $data[$flat] = [
                            'apartment_id' => $apartmentId,
                            'apartment' => $flat,
                            'maintenance_billed' => 0,
                            'maintenance_paid' => 0,
                            'utility_billed' => 0,
                            'utility_paid' => 0,
                        ];
                    }

                    $data[$flat]['maintenance_billed'] += $item->cost;
                    if ($item->paid_status === 'paid') {
                        $data[$flat]['maintenance_paid'] += $item->cost;
                    }
                }
            }

            $utilityBills = UtilityBillManagement::with('apartment')
                ->whereMonth('bill_date', $monthNumber)
                ->whereYear('bill_date', $this->selectedYear)
                ->when($apartmentIds, fn ($q) => $q->whereIn('apartment_id', $apartmentIds))
                ->get();
        } else {
            $maintenanceManagements = MaintenanceManagement::where('year', $this->selectedYear)->where('status', 'published')->get();

            foreach ($maintenanceManagements as $management) {
                $apartments = MaintenanceApartment::with('apartment')
                    ->where('maintenance_management_id', $management->id)
                    ->when($apartmentIds, fn ($q) => $q->whereIn('apartment_management_id', $apartmentIds))
                    ->get();
                foreach ($apartments as $item) {
                    $flat = $item->apartment->apartment_number;
                    $apartmentId = $item->apartment->id;
                    if (!isset($data[$flat])) {
                        $data[$flat] = [
                            'apartment_id' => $apartmentId,
                            'apartment' => $flat,
                            'maintenance_billed' => 0,
                            'maintenance_paid' => 0,
                            'utility_billed' => 0,
                            'utility_paid' => 0,
                        ];
                    }

                    $data[$flat]['maintenance_billed'] += $item->cost;
                    if ($item->paid_status === 'paid') {
                        $data[$flat]['maintenance_paid'] += $item->cost;
                    }
                }
            }

            $utilityBills = UtilityBillManagement::with('apartment')
                ->whereYear('bill_date', $this->selectedYear)
                ->when($apartmentIds, fn ($q) => $q->whereIn('apartment_id', $apartmentIds))
                ->get();
        }

        // Common utility bill loop for both cases
        foreach ($utilityBills as $bill) {
            $flat = $bill->apartment->apartment_number;
            $apartmentId = $bill->apartment->id;
            if (!isset($data[$flat])) {
                $data[$flat] = [
                    'apartment_id' => $apartmentId,
                    'apartment' => $flat,
                    'maintenance_billed' => 0,
                    'maintenance_paid' => 0,
                    'utility_billed' => 0,
                    'utility_paid' => 0,
                ];
            }

            $data[$flat]['utility_billed'] += $bill->bill_amount;
            if ($bill->status === 'paid') {
                $data[$flat]['utility_paid'] += $bill->bill_amount;
            }
        }

        // Finalize totals
        foreach ($data as &$row) {
            $row['total_paid'] = $row['maintenance_paid'] + $row['utility_paid'];
            $row['total_pending'] = ($row['maintenance_billed'] + $row['utility_billed']) - $row['total_paid'];
            $row['total_billed'] = $row['maintenance_billed'] + $row['utility_billed'];
        }

        
        $result = array_values($data);

        if (!empty($this->filterApartments)) {
            $result = array_filter($result, function ($row) {
                return in_array((string)$row['apartment_id'], $this->filterApartments);
            });
        }

        return $result;
    }
    public function updatedFilterApartments()
    {
        $this->clearFilterButton = true;
        $this->financialData = $this->generateReport();
    }

    public function downloadPdf()
    {
        $society = Society::with('currency')->first();

        $data = $this->reportType === 'month-wise' 
            ? ['data' => $this->financialData, 'type' => 'Month-wise', 'month' => $this->selectedMonth, 'year' => $this->selectedYear, 'society' => $society]
            : ['data' => $this->financialData, 'type' => 'Year-wise', 'year' => $this->selectedYear, 'society' => $society];

        $pdf = Pdf::loadView('maintenance.financial-report', $data);

        $fileName = $this->reportType === 'month-wise' 
        ? "financial-report-{$this->selectedMonth}-{$this->selectedYear}.pdf"
        : "financial-report-{$this->selectedYear}.pdf";


        return response()->streamDownload(
            fn () => print($pdf->output()),
            $fileName 
        );
    }

    public function render()
    {
        return view('livewire.reports.financial-report');
    }
}
