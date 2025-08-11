<?php

namespace App\Exports;

use App\Models\Floor;
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

class FloorExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{

    use Exportable;

    protected $search;
    protected $filterTower;

    public function __construct($search, $filterTower = [])
    {
        $this->search = $search;
        $this->filterTower = $filterTower;
    }

    public function headings(): array
    {
        return [
            __('modules.settings.societyFloor'),
            __('modules.settings.societyTower'),
        ];
    }

    public function map($floor): array
    {
        return [
            $floor->floor_name,
            $floor->tower->tower_name,
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
        $query = Floor::query();

        if (!empty($this->search)) {
            $query->where('floor_name', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->filterTower)) {
            $query->whereHas('tower', function ($q) {
                $q->whereIn('tower_name', $this->filterTower);
            });
        }

        return $query->get();    
    }

}
