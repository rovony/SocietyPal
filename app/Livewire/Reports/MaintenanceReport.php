<?php

namespace App\Livewire\Reports;

use App\Models\MaintenanceApartment;
use App\Models\MaintenanceManagement;
use App\Models\Society;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ApartmentManagement;
use App\Models\ApartmentTenant;

class MaintenanceReport extends Component
{
    public $reportType = 'month-wise'; 
    public $monthWiseData = [];
    public $yearWiseData = [];
    public $months = ['january', 'february', 'march', 'april', 'may', 'june','july', 'august', 'september', 'october', 'november', 'december' ];
    public $years = [];
    public $selectedMonth;
    public $selectedYear;

    public function mount()
    {
        $this->years = range(date('Y') - 5, date('Y'));
        $this->selectedMonth = strtolower(date('F'));
        $this->selectedYear = date('Y');
        $this->monthWiseData = $this->getMonthWiseData();
        $this->yearWiseData = $this->getYearWiseData();
    }

    private function getUserApartmentIds()
    {
        $user = user();

        if (isRole() == 'Admin') {
            return null;
        }

        $ownerApartmentIds = ApartmentManagement::where('user_id', $user->id)->pluck('id')->toArray();

        $tenantApartmentIds = ApartmentTenant::whereHas('tenant', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->pluck('apartment_id')->toArray();

        return array_unique(array_merge($ownerApartmentIds, $tenantApartmentIds));
    }

    public function getMonthWiseData()
    {
        $apartmentIds = $this->getUserApartmentIds();
        $maintenanceManagements = MaintenanceManagement::where('month', $this->selectedMonth)->where('year', $this->selectedYear)->where('status', 'published')->get();
        $data = [];

        foreach ($maintenanceManagements as $management) {
            $query = MaintenanceApartment::with('apartment')->where('maintenance_management_id', $management->id);
            if ($apartmentIds !== null) {
                $query->whereIn('apartment_management_id', $apartmentIds);
            }

            $apartments = $query->get();
            foreach ($apartments as $apartment) {
                $apartmentNumber = $apartment->apartment->apartment_number;
                $totalBilled = $apartment->cost;
                $totalPaid = $apartment->paid_status == 'paid' ? $apartment->cost : 0;
                $totalPending = $apartment->paid_status == 'unpaid' ? $apartment->cost : 0;
    
                $data[] = [
                    'apartment' => $apartmentNumber,
                    'billed' => $totalBilled,
                    'paid' => $totalPaid,
                    'pending' => $totalPending,
                ];
            }
        }

        return $data;
    }

    public function getYearWiseData()
    {
        $apartmentIds = $this->getUserApartmentIds();
        $maintenanceManagements = MaintenanceManagement::where('year', $this->selectedYear)->where('status', 'published')->get();

        $months = ['january', 'february', 'march', 'april', 'may', 'june','july', 'august', 'september', 'october', 'november', 'december' ];
        $apartmentData = [];

        foreach ($maintenanceManagements as $management) {
            $query = MaintenanceApartment::with('apartment')
                ->where('maintenance_management_id', $management->id);
            if ($apartmentIds !== null) {
                $query->whereIn('apartment_management_id', $apartmentIds);
            }

            $apartments = $query->get();

            foreach ($apartments as $apartment) {
                $apartmentNumber = $apartment->apartment->apartment_number;
                $monthIndex = date('n', strtotime($management->month)) - 1;

                if (!isset($apartmentData[$apartmentNumber])) {
                    $apartmentData[$apartmentNumber] = [
                        'apartment' => $apartmentNumber,
                        'months' => array_fill_keys($months, ['billed' => 0, 'paid' => 0, 'pending' => 0]),
                        'total' => ['billed' => 0, 'paid' => 0, 'pending' => 0]
                    ];
                }

                $monthName = $months[$monthIndex]; 
                $apartmentData[$apartmentNumber]['months'][$monthName]['billed'] += $apartment->cost;

                if ($apartment->paid_status == 'paid') {
                    $apartmentData[$apartmentNumber]['months'][$monthName]['paid'] += $apartment->cost;
                } elseif ($apartment->paid_status == 'unpaid') {
                    $apartmentData[$apartmentNumber]['months'][$monthName]['pending'] += $apartment->cost;
                }

                $apartmentData[$apartmentNumber]['total']['billed'] += $apartment->cost;
                if ($apartment->paid_status == 'paid') {
                    $apartmentData[$apartmentNumber]['total']['paid'] += $apartment->cost;
                } else {
                    $apartmentData[$apartmentNumber]['total']['pending'] += $apartment->cost;
                }
            }
        }

        $data = [];
        foreach ($apartmentData as $apartmentNumber => $details) {
            $row = [
                'apartment' => $details['apartment']
            ];

            foreach ($months as $month) {
                $row[$month] = $details['months'][$month]['billed'];
            }

            $row['total_billed'] = $details['total']['billed'];
            $row['total_paid'] = $details['total']['paid'];
            $row['total_pending'] = $details['total']['pending'];

            $data[] = $row;
        }

        return $data;
    }

    public function updatedSelectedMonth()
    {
        $this->monthWiseData = $this->getMonthWiseData();
    }

    public function updatedSelectedYear()
    {
        $this->yearWiseData = $this->getYearWiseData();
        $this->monthWiseData = $this->getMonthWiseData();
    }

    public function downloadPdf()
    {
        $society = Society::with('currency')->first();

        $data = $this->reportType === 'month-wise' 
            ? ['data' => $this->monthWiseData, 'type' => 'Month-wise', 'month' => $this->selectedMonth, 'year' => $this->selectedYear, 'society' => $society]
            : ['data' => $this->yearWiseData, 'type' => 'Year-wise', 'year' => $this->selectedYear, 'society' => $society];

        $pdf = Pdf::loadView('maintenance.maintenance-report', $data);

        $fileName = $this->reportType === 'month-wise' 
        ? "maintenance-report-{$this->selectedMonth}-{$this->selectedYear}.pdf"
        : "maintenance-report-{$this->selectedYear}.pdf";


        return response()->streamDownload(
            fn () => print($pdf->output()),
            $fileName 
        );
    }

    public function render()
    {
        return view('livewire.reports.maintenance-report');
    }
}
