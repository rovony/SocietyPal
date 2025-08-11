<?php

namespace App\Exports;

use App\Models\Rent;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RentsExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected $search;
    protected $filterYears;
    protected $filterMonths;
    protected $filterStatuses;

    public function __construct($search, $filterYears = [], $filterMonths = [], $filterStatuses = [])
    {
        $this->search = $search;
        $this->filterYears = $filterYears;
        $this->filterMonths = $filterMonths;
        $this->filterStatuses = $filterStatuses;
    }

    public function headings(): array
    {
        return [
            __('modules.settings.apartmentNumber'),
            __('modules.rent.rentFor'),
            __('modules.rent.tenantName'),
            __('modules.rent.rentAmount'),
            __('modules.rent.status'),
            __('modules.rent.paymentDate'),
        ];
    }

    public function map($rent): array
    {
        return [
            $rent->apartment->apartment_number,
            ucfirst($rent->rent_for_month) .' '. $rent->rent_for_year,
            $rent->tenant->user->name,
            currency_format($rent->rent_amount),
            $rent->status,
            $rent->payment_date ?? '--'
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
        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'name' => 'Arial',
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'f5f5f5'],
                ],
            ],
        ];
    }

    public function collection()
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


        if (!empty($this->filterYears)) {
            $query = $query->whereIn('rent_for_year', $this->filterYears);
        }

        if (!empty($this->filterMonths)) {
            $query = $query->whereIn('rent_for_month', $this->filterMonths);
        }

        if (!empty($this->filterStatuses)) {
            $query = $query->whereIn('status', $this->filterStatuses);
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

        return $query->get();
    }

}
