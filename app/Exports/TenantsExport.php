<?php

namespace App\Exports;

use App\Models\Tenant;
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

class TenantsExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{

    use Exportable;

    protected $search;
    protected $filterStatuses;

    public function __construct($search, $filterStatuses = [])
    {
        $this->search = $search;
        $this->filterStatuses = $filterStatuses;
    }

    public function headings(): array
    {
        return [
            __('modules.tenant.user'),
            __('modules.tenant.email'),
            __('modules.tenant.phone'),
            __('modules.user.role'),
            __('modules.rent.status'),
        ];
    }
    public function map($tenant): array
    {
        $phoneWithCode = '--';
        if ($tenant->user->phone_number) {
            $phoneWithCode = $tenant->user->country_phonecode
                ? '+' . $tenant->user->country_phonecode . ' ' . $tenant->user->phone_number
                : $tenant->user->phone_number;
        }

        return [
            $tenant->user->name,
            $tenant->user->email,
            $phoneWithCode,
            $tenant->user->role->display_name,
            $tenant->user->status,
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
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'name' => 'Arial'], 'fill'  => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => array('rgb' => 'f5f5f5'),
            ]],
        ];
    }

    public function collection()
    {
        $query = Tenant::query();

        if (!empty($this->search)) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%')
                ->orWhere('phone_number', 'like', '%'.$this->search.'%');
            });
        }

        if (!empty($this->filterStatuses)) {
            $query->whereHas('user', function ($q) {
                $q->whereIn('status', $this->filterStatuses);
            });
        }

        $query->whereHas('user.role', function ($q) {
            $q->whereIn('display_name', ['Tenant']);
        });

        return $query->get();
    }

}
