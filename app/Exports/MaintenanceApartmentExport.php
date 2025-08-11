<?php

namespace App\Exports;

use App\Models\MaintenanceApartment;
use App\Models\Tenant;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaintenanceApartmentExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{

    use Exportable;

    protected $search;
    protected $filterYears;
    protected $filterMonths;
    protected $filterStatus;

    public function __construct($search, $filterYears = [], $filterMonths = [], $filterStatus = [])
    {
        $this->search = $search;
        $this->filterYears = $filterYears;
        $this->filterMonths = $filterMonths;
        $this->filterStatus = $filterStatus;
    }

    public function headings(): array
    {
        return [
            __('app.year'),
            __('app.month'),
            __('modules.settings.apartmentNumber'),
            __('modules.maintenance.totalCost'),
            __('modules.maintenance.paymentDueDate'),
            __('modules.maintenance.paymentDate'),
            __('app.status'),
        ];
    }

    public function map($maintenance): array
    {
        return [
            $maintenance->maintenanceManagement->year,
            ucFirst($maintenance->maintenanceManagement->month),
            $maintenance->apartment->apartment_number,
            currency_format($maintenance->cost),
            $maintenance->maintenanceManagement->payment_due_date,
            $maintenance->payment_date ?? '--',
            ucFirst($maintenance->paid_status),
        ]; 
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return $defaultStyle
            ->getFont()
            ->setName('Arial');
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'name' => 'Arial'], 'fill'  => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => array('rgb' => 'f5f5f5'),
            ]],
        ];
    }

    public function collection()
    {
        $userId = user()->id;

        $query = MaintenanceApartment::query()->with(['maintenanceManagement', 'apartment', 'tenants']);
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

            if ($this->search) {
                $query->where(function ($mainQuery) {
                    $mainQuery->where('paid_status', 'like', '%' . $this->search . '%') // Direct field
                        ->orWhereHas('maintenanceManagement', function ($subQuery) {
                            $subQuery->where('year', 'like', '%' . $this->search . '%')
                                ->orWhere('month', 'like', '%' . $this->search . '%');
                        });
                });
            }
            
            if (!empty($this->filterYears)) {
                $query->whereHas('maintenanceManagement', function ($subQuery) {
                    $subQuery->whereIn('year', $this->filterYears);
                });
            }
    
            if (!empty($this->filterMonths)) {
                $query->whereHas('maintenanceManagement', function ($subQuery) {
                    $subQuery->whereIn('month', $this->filterMonths);
                });
            }
    
            if (!empty($this->filterStatus)) {
                $query->whereIn('paid_status', $this->filterStatus);
            }

            return $query->get();

    }
            
}
