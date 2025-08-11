<?php

namespace App\Exports;

use App\Models\AssetMaintenance;
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

class AssetMaintenanceExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected $search;
    protected $filterStatus;

    public function __construct($search = null, $filterStatus = [])
    {
        $this->search = $search;
        $this->filterStatus = $filterStatus;
    }

    public function headings(): array
    {
        return [
            __('modules.assets.asset'),
            __('modules.assets.maintenanceDate'),
            __('modules.assets.maintenanceSchedule'),
            __('modules.assets.status'),
            __('modules.assets.amount'),
        ];
    }

    public function map($maintenance): array
    {
        return [
            $maintenance->asset->name ?? '--',
            $maintenance->maintenance_date,
            ucfirst($maintenance->schedule),
            ucfirst($maintenance->status),
            $maintenance->amount ?? 0,
        ];
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return $defaultStyle->getFont()->setName('Arial');
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
        $query = AssetMaintenance::query()
            ->with('asset', 'asset.apartments');
            $loggedInUser = user()->id;

            if (!user_can('Show Assets')) {
                $query->whereHas('asset', function ($assetQuery) use ($loggedInUser) {
                    $assetQuery->whereHas('apartments', function ($q) use ($loggedInUser) {
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
            if (!empty($this->filterStatus)) {
                $query->where(function ($q) {
                    foreach ($this->filterStatus as $status) {
                        $q->orWhere('status', $status);
                    }
                });
            }


        if (!empty($this->search)) {
            $query->whereHas('asset', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });

        }

        return $query->get();
    }
}
