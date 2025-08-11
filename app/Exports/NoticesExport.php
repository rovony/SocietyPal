<?php

namespace App\Exports;

use App\Models\Notice;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NoticesExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{

    use Exportable;

    protected $search;
    protected $filterRoles;

    public function __construct($search, $filterRoles = [])
    {
        $this->search = $search;
        $this->filterRoles = $filterRoles;
    }

    public function headings(): array
    {
        return [
            __('modules.notice.title'),
            __('modules.notice.roles'),

        ];
    }

    public function map($notice): array
    {
        $roleNames = $notice->roles->pluck('display_name')->implode(', ');
        return [
            $notice->title,
            $roleNames
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
        $query = Notice::with('roles');
        if (!empty($this->filterRoles)) {
            $query->whereHas('noticeRoles', function ($query) {
                $query->whereIn('role_id', $this->filterRoles);
            });
        }

        if ($this->search != '') {
            $query = $query->where('title', 'like', '%' . $this->search . '%');
        }
        return $query->get();
    }

}
