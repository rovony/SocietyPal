<?php

namespace App\Exports;

use App\Models\ApartmentManagement;
use App\Models\User;
use App\Models\VisitorManagement;
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

class ApartmentExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{

    use Exportable;

    protected $search;
    protected $filterApartmentsType;
    protected $filterTower;
    protected $filterFloor;
    protected $filterStatuses;

    public function __construct($search, $filterApartmentsType = [], $filterTower = [], $filterFloor = [], $filterStatuses = [])
    {
        $this->search = $search;
        $this->filterApartmentsType = $filterApartmentsType;
        $this->filterTower = $filterTower;
        $this->filterFloor = $filterFloor;
        $this->filterStatuses = $filterStatuses;
    }

    public function headings(): array
    {
        return [
            __('modules.settings.societyApartmentNumber'),
            __('modules.settings.societyApartmentArea'),
            __('modules.settings.societyApartmentStatus'),
            __('modules.settings.apartmentType'),
            __('modules.settings.societyTower'),
            __('modules.settings.floorName'),
            __('modules.user.name'),
            __('modules.rent.tenantName'),
        ];
    }

    public function map($apartment): array
    {

        return [
            $apartment->apartment_number,
            $apartment->apartment_area,
            $apartment->status,
            $apartment->apartments->apartment_type,
            $apartment->towers->tower_name,
            $apartment->floors->floor_name,
            $apartment->user->name ?? '--',
            $apartment->tenants->first()->user->name ?? '--',

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
        $query = ApartmentManagement::query()->with('tenants.user','user');

        if (!empty($this->search)) {
            $query = $query->with('towers','floors','apartments')
            ->orWhereHas('towers', function ($query) {
                $query->where('tower_name', 'like', '%'.$this->search.'%');
            })
            ->orWhereHas('floors', function ($query) {
                $query->where('floor_name', 'like', '%'.$this->search.'%');
            })
            ->orWhereHas('apartments', function ($query) {
                $query->where('apartment_type', 'like', '%'.$this->search.'%');
            })
            ->orwhere('apartment_number', 'like', '%' . $this->search . '%')
            ->orWhere('apartment_area', 'like', '%' . $this->search . '%',)
            ->orWhere('status', 'like', '%' . $this->search . '%',);

        }

        if (!user_can('Show Apartment')) {
            $query->where('user_id', user()->id);
        }

        if (!empty($this->filterApartmentsType)) {
            $query->whereHas('apartments', function ($query) {
                $query->whereIn('apartment_type', $this->filterApartmentsType);
            });
        }

        if (!empty($this->filterTower)) {
            $query->whereHas('towers', function ($query) {
                $query->whereIn('tower_name', $this->filterTower);
            });
        }

        if (!empty($this->filterFloor)) {
            $query->whereHas('floors', function ($query) {
                $query->whereIn('floor_name', $this->filterFloor);
            });
        }

        if (!empty($this->filterStatuses)) {
            $query->whereIn('status', $this->filterStatuses);
        }

        return $query->get();
    }

}
