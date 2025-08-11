<?php

namespace App\Exports;

use App\Models\AssetManagement;
use App\Models\Assets;
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

class AssetExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{

    use Exportable;

    protected $search;
    protected $filterCategories;
    protected $filterTower;

    public function __construct($search, $filterCategories = [], $filterTower = [])
    {
        $this->search = $search;
        $this->filterCategories = $filterCategories;
        $this->filterTower = $filterTower;
    }

    public function headings(): array
    {
        return [
            __('modules.assets.assetName'),
            __('modules.assets.category'),
            __('modules.assets.tower'),
            __('modules.assets.floor'),
            __('modules.assets.apartment'),
            __('modules.assets.condition'),

        ];
    }

    public function map($assets): array
    {
        return [
            ucFirst($assets->name),
            $assets->category->name,
            $assets->towers->tower_name ?? '--',
            $assets->floors->floor_name ?? '--',
            $assets->apartments->apartment_number ?? '--',
            ucFirst($assets->condition),

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
        $query = AssetManagement::query();
        $loggedInUser = user()->id;

        if (!user_can('Show Assets')) {
            $query->where(function ($subQuery) use ($loggedInUser) {
                $subQuery->whereHas('apartments', function ($q) use ($loggedInUser) {
                    $q->where('user_id', $loggedInUser);
                })
                ->orWhereHas('apartments', function ($q) use ($loggedInUser) {
                    $q->where('status', 'rented')
                        ->whereHas('tenants', function ($q) use ($loggedInUser) {
                            $q->where('user_id', $loggedInUser);
                        });
                });
            });
        }
        
        if (!empty($this->search)) {
            $query->where(function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%')
              ->orWhereHas('apartments', function ($subQuery) {
                  $subQuery->where('apartment_number', 'like', '%' . $this->search . '%');
              });
            });
        }

        if (!empty($this->filterCategories)) {
            $query->whereHas('category', function ($q) {
            $q->whereIn('id', $this->filterCategories);
            });
        }
        if (!empty($this->filterTower)) {
            $query->whereHas('towers', function ($query) {
                $query->whereIn('tower_name', $this->filterTower);
            });
        }


        return $query->get();
    }

}
