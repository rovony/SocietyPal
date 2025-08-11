<?php

namespace App\Exports;

use App\Models\AssetIssue;
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

class AssetIssueExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected $search;
    protected $filterStatus;
    protected $filterPriority;

    public function __construct($search = null, $filterStatus = null, $filterPriority = null)
    {
        $this->search = $search;
        $this->filterStatus = $filterStatus;
        $this->filterPriority = $filterPriority;
    }

    public function headings(): array
    {
        return [
            __('modules.assets.issueTitle'),
            __('modules.assets.issueStatus'),
            __('modules.assets.priority'),
            __('modules.assets.asset'),
        ];
    }

    public function map($issue): array
    {
        return [
            $issue->title,
            ucfirst($issue->status),
            ucfirst($issue->priority),
            optional($issue->asset)->name ?? '--',
        ];
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return $defaultStyle->getFont()->setName('Arial');
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        return [
            1 => [
                'font' => ['bold' => true, 'name' => 'Arial'],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'f5f5f5'],
                ],
            ],
        ];
    }

    public function collection()
    {
        $query = AssetIssue::query()
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

        if (!empty($this->search)) {
            $query->where(function ($q) {
            $q->whereHas('asset', function ($subQuery) {
                $subQuery->where('name', 'like', '%' . $this->search . '%');
            })
            ->orWhere('title', 'like', '%' . $this->search . '%');
            });

        }

        if (!empty($this->filterStatus)) {
            $query->where(function ($q) {
                foreach ($this->filterStatus as $status) {
                    $q->orWhere('status', $status);
                }
            });
        }

        if (!empty($this->filterPriority)) {
            $query->where(function ($q) {
                foreach ($this->filterPriority as $priority) {
                    $q->orWhere('priority', $priority);
                }
            });
        }

        return $query->get();
    }
}
