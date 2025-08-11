<?php

namespace App\Exports;

use App\Models\MaintenanceApartment;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaintenanceDetailExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{

    use Exportable;

    protected $maintenanceId;
    protected $filterApartments;
    protected $filterStatus;

    public function __construct($maintenanceId, $filterApartments = [], $filterStatus = [])
    {
        $this->maintenanceId = $maintenanceId;
        $this->filterApartments = $filterApartments;
        $this->filterStatus = $filterStatus;
    }

    public function headings(): array
    {
        return [
            __('modules.settings.apartmentNumber'),
            __('modules.maintenance.totalCost'),
            __('app.status'),
            __('modules.maintenance.paymentDate'),
        ];
    }

    public function map($maintenance): array
    {
        return [
            $maintenance->apartment->apartment_number,
            currency_format($maintenance->cost),
            ucFirst($maintenance->paid_status),
            $maintenance->payment_date ?? '--',
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
        $query = MaintenanceApartment::query()->where('maintenance_management_id', $this->maintenanceId)
        ->with('apartment');

        if (!empty($this->filterApartments)) {
            $query->whereHas('apartment', function ($query) {
                $query->whereIn('apartment_number', $this->filterApartments);
            });
        }

        if (!empty($this->filterStatus)) {
            $query->whereIn('paid_status', $this->filterStatus);
        }

        return $query->get();
    }
            
}
