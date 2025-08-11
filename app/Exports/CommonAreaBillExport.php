<?php

namespace App\Exports;

use App\Models\User;
use App\Models\CommonAreaBills;
use App\Models\VisitorManagement;
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

class CommonAreaBillExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{

    use Exportable;

    protected $search;
    protected $startDate;
    protected $endDate;
    protected $filterBillTypes;
    protected $filterStatuses;

    public function __construct($search, $startDate, $endDate, $filterBillTypes = [], $filterStatuses = [])
    {
        $this->search = $search;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->filterBillTypes = $filterBillTypes;
        $this->filterStatuses = $filterStatuses;
    }

    public function headings(): array
    {
        return [
            __('modules.settings.billType'),
            __('modules.utilityBills.billDate'),
            __('modules.settings.status'),
            __('modules.utilityBills.billAmount'),
            __('modules.utilityBills.billPaymentDate'),
        ];
    }

    public function map($commonAreaBill): array
    {
        return [
            $commonAreaBill->billType->name,
            $commonAreaBill->bill_date->format('d-m-Y'),
            $commonAreaBill->status,
            currency_format($commonAreaBill->bill_amount),
            $commonAreaBill->bill_payment_date?->format('d-m-Y'),
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
        }

        if (!empty($this->filterStatuses)) {
            $query->whereIn('status', $this->filterStatuses);
        }

        return $query->get();
    }

}
