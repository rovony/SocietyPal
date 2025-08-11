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

class OwnersExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{

    use Exportable;

    protected $filterStatus;
    protected $search;
    protected $filterApartment;

    public function __construct($search, $filterStatus = [], $filterApartment = [])
    {
        $this->search = $search;
        $this->filterStatus = $filterStatus;
        $this->filterApartment = $filterApartment;
    }

    public function headings(): array
    {
        return [
            __('app.user'),
            __('modules.user.email'),
            __('modules.user.phone'),
            __('modules.user.role'),
            __('modules.user.status'),
            __('modules.settings.apartmentNumber'),
        ];
    }

    public function map($owner): array
    {
        $phoneWithCode = '--';
        if ($owner->phone_number) {
            $phoneWithCode = $owner->country_phonecode
                ? '+' . $owner->country_phonecode . ' ' . $owner->phone_number
                : $owner->phone_number;
        }

        return [
            ucfirst($owner->name),
            $owner->email,
            $phoneWithCode,
            ucfirst($owner->role->display_name),
            ucfirst($owner->status),
            $owner->apartment->isNotEmpty() ? $owner->apartment->pluck('apartment_number')->implode(', ') : '--',
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

        if (!empty($this->filterStatus)) {
            $query->where(function ($q) {
                foreach ($this->filterStatus as $status) {
                    $q->orWhere('status', $status);
                }
            });
        }

        if (!empty($this->filterApartment)) {
            $query->whereHas('apartment', function ($q) {
                $q->whereIn('id', $this->filterApartment);
            });
        }

        $query->whereHas('role', function ($q) {
            $q->whereIn('display_name', ['Owner']);
        });

        return $query->get();
    }

}
