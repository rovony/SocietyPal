<?php

namespace App\Exports;

use App\Models\MaintenanceManagement;
use App\Models\Notice;
use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaintenanceExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{

    use Exportable;

    protected $search;
    protected $filterYears;
    protected $filterMonths;

    public function __construct($search, $filterYears = [], $filterMonths = [])
    {
        $this->search = $search;
        $this->filterYears = $filterYears;
        $this->filterMonths = $filterMonths;
    }

    public function headings(): array
    {
        return [
            __('app.year'),
            __('app.month'),
            __('modules.maintenance.totalAdditionalCost'),
            __('app.status'),
        ];
    }

    public function map($maintenance): array
    {
        return [
            $maintenance->year,
            ucFirst($maintenance->month),
            currency_format($maintenance->total_additional_cost),
            ucFirst($maintenance->status),
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
        $query = MaintenanceManagement::query();

        if ($this->search) {
            $query = $query->where('year', 'like', '%' . $this->search . '%')
                            ->orWhere('month', 'like', '%' . $this->search . '%')
                            ->orWhere('status', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->filterYears)) {
            $query = $query->whereIn('year', $this->filterYears);
        }

        if (!empty($this->filterMonths)) {
            $query = $query->whereIn('month', $this->filterMonths);
        }
        return $query->get();    
    }

}
