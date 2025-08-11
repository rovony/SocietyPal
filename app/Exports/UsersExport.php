<?php

namespace App\Exports;

use App\Models\User;
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

class UsersExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{

    use Exportable;

    protected $search;
    protected $filterRoles;
    protected $filterStatus;

    public function __construct($search, $filterRoles = [], $filterStatus = [])
    {
        $this->search = $search;
        $this->filterRoles = $filterRoles;
        $this->filterStatus = $filterStatus;
    }

    public function headings(): array
    {
        return [
            __('app.user'),
            __('modules.user.email'),
            __('modules.user.phone'),
            __('modules.user.role'),
            __('modules.user.status'),
        ];
    }

    public function map($user): array
    {
        $phoneWithCode = '--';
        if ($user->phone_number) {
            $phoneWithCode = $user->country_phonecode
                ? '+' . $user->country_phonecode . ' ' . $user->phone_number
                : $user->phone_number;
        }

        return [
            ucFirst($user->name),
            $user->email,
            $phoneWithCode,
            ucFirst($user->role->display_name),
            ucFirst($user->status),
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
        $query = User::query();

        if (!empty($this->search)) {
            $query->where(function ($subQuery) {
                $subQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone_number', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->filterRoles)) {
            $query->whereHas('roles', function ($q) {
                $q->whereIn('id', $this->filterRoles);
            });
        }

        if (!empty($this->filterStatus)) {
            $query->where(function ($q) {
                foreach ($this->filterStatus as $status) {
                    $q->orWhere('status', $status);
                }
            });
        }

        $query->whereDoesntHave('role', function ($q) {
            $q->whereIn('display_name', ['Owner', 'Tenant']);
        });

        return $query->get();
    }

}
