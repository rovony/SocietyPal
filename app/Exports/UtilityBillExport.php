<?php

namespace App\Exports;

use App\Models\User;
use App\Models\UtilityBillManagement;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UtilityBillExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{

    use Exportable;

    protected $search;
    protected $startDate;
    protected $endDate;
    protected $filterApartments;
    protected $filterBillTypes;
    protected $filterStatuses;

    public function __construct($search, $startDate, $endDate, $filterApartments = [], $filterBillTypes = [], $filterStatuses = [])
    {
        $this->search = $search;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->filterApartments = $filterApartments;
        $this->filterBillTypes = $filterBillTypes;
        $this->filterStatuses = $filterStatuses;
    }


    public function headings(): array
    {
        return [
            __('modules.settings.societyApartmentNumber'),
            __('modules.settings.billType'),
            __('modules.utilityBills.billDate'),
            __('modules.utilityBills.billAmount'),
            __('modules.utilityBills.billPaymentDate'),
            __('modules.settings.status'),
        ];
    }

    public function map($utilityBill): array
    {
        return [
            $utilityBill->apartment->apartment_number,
            $utilityBill->billType->name,
            $utilityBill->bill_date->format('d-m-Y'),
            currency_format($utilityBill->bill_amount),
            $utilityBill->bill_payment_date ? $utilityBill->bill_payment_date->format('d-m-Y') : '--',
            $utilityBill->status,
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
        }

        if (!empty($this->filterBillTypes)) {
            $query->whereHas('billType', function ($query) {
                $query->whereIn('name', $this->filterBillTypes);
            });
        }

        if (!empty($this->filterStatuses)) {
            $query->whereIn('status', $this->filterStatuses);
        }

        return $query->get();
    }

}
